<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use App\Message\Api\Study\FindStudiesByUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function is_null;

class CastorStudiesApiController extends ApiController
{
    /**
     * @Route("/api/castor/studies", name="api_castor_studies")
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), true, ! is_null($request->get('hide'))));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimeOutException) {
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoPermissionException) {
                return new JsonResponse($e->toArray(), 403);
            }
        }
    }
}
