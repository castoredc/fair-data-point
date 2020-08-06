<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\UserApiRequest;
use App\Api\Resource\Security\UserApiResource;
use App\Exception\ApiRequestParseError;
use App\Message\Security\UpdateUserCommand;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user")
 */
class UserApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_user")
     */
    public function user(): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return new JsonResponse(null);
        }

        return new JsonResponse((new UserApiResource($user))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_user_update")
     */
    public function updateUser(Request $request, MessageBusInterface $bus): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        if (! $user->getNameOrigin()->isOrcid()) {
            throw new AccessDeniedHttpException();
        }

        try {
            /** @var UserApiRequest $parsed */
            $parsed = $this->parseRequest(UserApiRequest::class, $request);
            $bus->dispatch(
                new UpdateUserCommand($parsed->getFirstName(), $parsed->getMiddleName(), $parsed->getLastName(), $parsed->getEmail())
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating the user\'s information', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }
}
