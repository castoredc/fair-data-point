<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\EditPermissionApiRequest;
use App\Api\Request\Security\PermissionApiRequest;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Security\PermissionApiResource;
use App\Command\Security\AddPermissionToEntityCommand;
use App\Command\Security\EditPermissionToEntityCommand;
use App\Command\Security\RemovePermissionToEntityCommand;
use App\Entity\Enum\PermissionsEnabledEntityType;
use App\Entity\PaginatedResultCollection;
use App\Exception\ApiRequestParseError;
use App\Exception\UserAlreadyExists;
use App\Exception\UserNotFound;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/permissions/{type}/{objectId}")
 */
class PermissionsApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_permissions")
     */
    public function permissions(string $type, string $objectId): Response
    {
        $object = $this->getObject($type, $objectId);
        $this->denyAccessUnlessGranted('manage', $object);

        return new JsonResponse(
            (new PaginatedApiResource(
                PermissionApiResource::class,
                new PaginatedResultCollection(
                    $object->getPermissions()->toArray(),
                    1,
                    $object->getPermissions()->count(),
                    $object->getPermissions()->count(),
                ),
                $this->isGranted('ROLE_ADMIN')
            ))->toArray()
        );
    }

    /**
     * @Route("", methods={"POST"}, name="api_permissions_add")
     */
    public function addPermissions(string $type, string $objectId, Request $request, MessageBusInterface $bus): Response
    {
        $object = $this->getObject($type, $objectId);
        $this->denyAccessUnlessGranted('manage', $object);

        try {
            $parsed = $this->parseRequest(PermissionApiRequest::class, $request);
            assert($parsed instanceof PermissionApiRequest);

            $envelope = $bus->dispatch(new AddPermissionToEntityCommand($object, $parsed->getEmail(), $parsed->getType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new PermissionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof UserAlreadyExists) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            if ($e instanceof UserNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while adding permissions for a user', [
                'exception' => $e,
                'dataModel' => $object->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{user}", methods={"POST"}, name="api_model_permissions_edit")
     * @ParamConverter("user", options={"mapping": {"user": "id"}})
     */
    public function editPermissions(string $type, string $objectId, User $user, Request $request, MessageBusInterface $bus): Response
    {
        $object = $this->getObject($type, $objectId);
        $this->denyAccessUnlessGranted('manage', $object);

        try {
            $parsed = $this->parseRequest(EditPermissionApiRequest::class, $request);
            assert($parsed instanceof EditPermissionApiRequest);

            $envelope = $bus->dispatch(new EditPermissionToEntityCommand($object, $user, $parsed->getType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new PermissionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof UserNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while changing permissions for a user', [
                'exception' => $e,
                'dataModel' => $object->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{user}", methods={"DELETE"}, name="api_model_permissions_remove")
     * @ParamConverter("user", options={"mapping": {"user": "id"}})
     */
    public function removePermissions(string $type, string $objectId, User $user, MessageBusInterface $bus): Response
    {
        $object = $this->getObject($type, $objectId);
        $this->denyAccessUnlessGranted('manage', $object);

        try {
            $bus->dispatch(new RemovePermissionToEntityCommand($object, $user));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while removing permissions for a user', [
                'exception' => $e,
                'dataModel' => $object->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getObject(string $type, string $objectId): PermissionsEnabledEntity
    {
        $type = PermissionsEnabledEntityType::fromString($type);

        $object = $this->em->getRepository($type->getClass())->find($objectId);
        assert($object instanceof PermissionsEnabledEntity);

        return $object;
    }
}
