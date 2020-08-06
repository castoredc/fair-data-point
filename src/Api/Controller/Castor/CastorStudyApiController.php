<?php
declare(strict_types=1);

namespace App\Api\Controller\Castor;

use App\Api\Controller\ApiController;
use App\Api\Resource\Study\StudiesApiResource;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Message\Study\FindStudiesByUserCommand;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorStudyApiController extends ApiController
{
    /**
     * @Route("/api/castor/studies", name="api_castor_studies")
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, true, $request->get('hide') !== null));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new StudiesApiResource($handledStamp->getResult(), false))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            $this->logger->critical('An error occurred while loading the studies from Castor', ['exception' => $e]);
        }

        return new JsonResponse([], 500);
    }
}
