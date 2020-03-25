<?php

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Exception\ApiRequestParseException;
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

class StudiesApiController extends ApiController
{
    /**
     * @Route("/api/study", name="api_studies")
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function studies(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), false));
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult());
    }

    /**
     * @Route("/api/study/add", methods={"POST"}, name="api_add_study")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function addCastorStudy(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var CastorStudyApiRequest $parsed */
            $parsed = $this->parseRequest(CastorStudyApiRequest::class, $request);

            $envelope = $bus->dispatch(new AddCastorStudyCommand($parsed->getStudyId(), $this->getUser()));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch(HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyAlreadyExistsException) {
                return new JsonResponse($e->toArray(), 409);
            }
        }
    }
}