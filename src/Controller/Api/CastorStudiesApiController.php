<?php

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Exception\ApiRequestParseException;
use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use App\Exception\StudyAlreadyExistsException;
use App\Message\Api\Study\AddCastorStudyCommand;
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
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), true, $request->get('hide')));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult());
        } catch(HandlerFailedException $e) {
            $e = $e->getPrevious();

            if($e instanceof SessionTimeOutException)
            {
                return new JsonResponse($e->toArray(), 401);
            }
            else if($e instanceof NoPermissionException)
            {
                return new JsonResponse($e->toArray(), 403);
            }
        }
    }
}