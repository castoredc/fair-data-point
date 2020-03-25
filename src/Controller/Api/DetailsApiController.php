<?php

namespace App\Controller\Api;

use App\Api\Request\StudyMetadataApiRequest;
use App\Controller\Api\ApiController;
use App\Exception\ApiRequestParseException;
use App\Message\Api\Study\CreateStudyMetadataCommand;
use App\Message\Api\Study\GetStudyMetadataCommand;
use App\Message\Api\Study\UpdateStudyMetadataCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class DetailsApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/metadata", methods={"GET"}, name="api_get_metadata")
     * @param string              $studyId
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function getMetadata(string $studyId, Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new GetStudyMetadataCommand(
                                           $studyId,
                                           $this->getUser()
                                       ));

            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch(HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/add", methods={"POST"}, name="api_add_metadata")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function addMetadata(string $studyId, Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new CreateStudyMetadataCommand(
                    $studyId,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    null,
                    $parsed->getType(),
                    $parsed->getCondition(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->getUser()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/{metadataId}/update", methods={"POST"}, name="api_update_metadata")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function updateMetadata(string $studyId, string $metadataId, Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new UpdateStudyMetadataCommand(
                    $metadataId,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    null,
                    $parsed->getType(),
                    $parsed->getCondition(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->getUser()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}