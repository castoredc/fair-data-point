<?php
declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Request\ApiRequest;
use App\Api\Request\DynamicApiRequest;
use App\Api\Resource\ApiResource;
use App\Api\Resource\RoleBasedApiResource;
use App\Api\Resource\Security\PermissionsApiResource;
use App\Command\FAIRData\GetAssociatedItemCountCommand;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\AssociatedItemCount;
use App\Entity\PaginatedResultCollection;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Model\Castor\ApiClient;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_merge;
use function assert;
use function count;
use function json_decode;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected ApiClient $apiClient,
        protected ValidatorInterface $validator,
        protected LoggerInterface $logger,
        protected EntityManagerInterface $em,
        protected MessageBusInterface $bus,
    ) {
    }

    /** @throws ApiRequestParseError */
    protected function parseRequest(string $requestObject, Request $request, ?object $context = null): ApiRequest
    {
        $request = new $requestObject($request);
        assert($request instanceof ApiRequest);

        if ($context !== null) {
            $request->setContext($context);
        }

        $errors = $this->validator->validate($request);

        if ($errors->count() > 0) {
            throw new ApiRequestParseError($errors);
        }

        return $request;
    }

    /**
     * @return array<object>
     *
     * @throws GroupedApiRequestParseError
     */
    protected function parseGroupedRequest(string $requestObject, Request $request): array
    {
        $groupedErrors = [];
        $return = [];

        foreach (json_decode($request->getContent(), true) as $index => $item) {
            $object = new $requestObject($request, $index);
            $errors = $this->validator->validate($object);

            if ($errors->count() > 0) {
                $groupedErrors[$index] = $errors;
            }

            $return[] = $object;
        }

        if (count($groupedErrors) > 0) {
            throw new GroupedApiRequestParseError($groupedErrors);
        }

        return $return;
    }

    /** @throws ApiRequestParseError */
    protected function parseDynamicRequest(string $requestObject, Request $request, object $context): ApiRequest
    {
        $request = new $requestObject($request);
        assert($request instanceof DynamicApiRequest);

        $request->setContext($context);

        $errors = $this->validator->validate($request->getValues(), $request->getConstraints());

        if ($errors->count() > 0) {
            throw new ApiRequestParseError($errors);
        }

        return $request;
    }

    /** @param string[]|null $attributes */
    protected function getResponse(ApiResource $resource, ?object $object, ?array $attributes): JsonResponse
    {
        if ($object === null || $attributes === null) {
            return new JsonResponse($resource->toArray());
        }

        return new JsonResponse(array_merge($resource->toArray(), $this->getPermissionArray($object, $attributes)));
    }

    /** @param string[]|null $attributes */
    protected function getPaginatedResponse(
        string $apiResourceType,
        PaginatedResultCollection $collection,
        ?array $attributes = null,
        ?UriHelper $uriHelper = null,
    ): JsonResponse {
        $results = [];

        foreach ($collection as $object) {
            if ($uriHelper !== null) {
                $resource = new $apiResourceType($object, $uriHelper);
            } else {
                $resource = new $apiResourceType($object);
            }

            if ($resource instanceof RoleBasedApiResource) {
                $resource->setAdmin($this->isGranted('ROLE_ADMIN'));
            }

            assert($resource instanceof ApiResource);

            $apiResource = $resource->toArray();

            if ($attributes !== null) {
                $apiResource = array_merge($apiResource, $this->getPermissionArray($object, $attributes));
            }

            $results[] = $apiResource;
        }

        return new JsonResponse(
            [
                'results' => $results,
                'currentPage' => $collection->getCurrentPage(),
                'start' => $collection->getStart(),
                'perPage' => $collection->getPerPage(),
                'totalResults' => $collection->getTotalResults(),
                'totalPages' => $collection->getTotalPages(),
            ]
        );
    }

    /**
     * @param string[] $attributes
     *
     * @return array<string, string[]>
     */
    protected function getPermissionArray(object $object, array $attributes): array
    {
        $permissions = [];

        foreach ($attributes as $attribute) {
            if (! $this->isGranted($attribute, $object)) {
                continue;
            }

            $permissions[] = $attribute;
        }

        return (new PermissionsApiResource($permissions))->toArray();
    }

    /** @param string[]|null $attributes */
    protected function getResponseWithAssociatedItemCount(ApiResource $resource, AccessibleEntity $object, ?array $attributes): JsonResponse
    {
        try {
            $envelope = $this->bus->dispatch(
                new GetAssociatedItemCountCommand($object)
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();
            assert($results instanceof AssociatedItemCount);

            $array = array_merge($resource->toArray(), $this->getPermissionArray($object, $attributes));
            $array['count'] = $results->getCounts();

            return new JsonResponse($array);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the associated items',
                ['exception' => $e]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
