<?php
declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Request\ApiRequest;
use App\Api\Resource\ApiResource;
use App\Api\Resource\RoleBasedApiResource;
use App\Api\Resource\Security\PermissionsApiResource;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_merge;
use function assert;
use function count;
use function json_decode;

abstract class ApiController extends AbstractController
{
    protected ApiClient $apiClient;

    protected ValidatorInterface $validator;

    protected LoggerInterface $logger;

    protected EntityManagerInterface $em;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->em = $em;
    }

    /** @throws ApiRequestParseError */
    protected function parseRequest(string $requestObject, Request $request): ApiRequest
    {
        $request = new $requestObject($request);

        $errors = $this->validator->validate($request);

        if ($errors->count() > 0) {
            throw new ApiRequestParseError($errors);
        }

        assert($request instanceof ApiRequest);

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
        ?UriHelper $uriHelper = null
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

        return new JsonResponse([
            'results' => $results,
            'currentPage' => $collection->getCurrentPage(),
            'start' => $collection->getStart(),
            'perPage' => $collection->getPerPage(),
            'totalResults' => $collection->getTotalResults(),
            'totalPages' => $collection->getTotalPages(),
        ]);
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
}
