<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Message\Api\Study\FindStudiesByUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorStudiesApiController extends ApiController
{
    /**
     * @Route("/api/castor/studies", name="api_castor_studies")
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), true, ! $request->get('hide') !== null));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }
        }
    }
}
