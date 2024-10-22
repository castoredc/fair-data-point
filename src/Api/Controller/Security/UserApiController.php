<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\UserApiRequest;
use App\Api\Resource\Security\UserApiResource;
use App\Command\Security\UpdateUserCommand;
use App\Exception\ApiRequestParseError;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/user')]
class UserApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_user')]
    public function user(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        if ($user === null) {
            return new JsonResponse(null);
        }

        return new JsonResponse((new UserApiResource($user))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_user_update')]
    public function updateUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        if (! $user->hasOrcid()) {
            throw new AccessDeniedHttpException();
        }

        if ($user->getPerson() !== null && ! $user->getPerson()->getNameOrigin()->isOrcid()) {
            throw new AccessDeniedHttpException();
        }

        try {
            $parsed = $this->parseRequest(UserApiRequest::class, $request);
            assert($parsed instanceof UserApiRequest);
            $this->bus->dispatch(
                new UpdateUserCommand(
                    $parsed->getFirstName(),
                    $parsed->getLastName(),
                    $parsed->getEmail(),
                    $parsed->getMiddleName()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating the user\'s information', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
