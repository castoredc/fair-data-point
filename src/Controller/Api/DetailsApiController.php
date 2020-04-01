<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Metadata\StudyMetadataApiRequest;
use App\Entity\Castor\Study;
use App\Entity\Metadata\StudyMetadata;
use App\Exception\ApiRequestParseError;
use App\Message\Metadata\CreateStudyMetadataCommand;
use App\Message\Metadata\GetStudyMetadataCommand;
use App\Message\Metadata\UpdateStudyMetadataCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getMetadata(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            $envelope = $bus->dispatch(new GetStudyMetadataCommand(
                $study,
                $user
            ));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/add", methods={"POST"}, name="api_add_metadata")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function addMetadata(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var StudyMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);

            $bus->dispatch(
                new CreateStudyMetadataCommand(
                    $study,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getSummary() : null,
                    $parsed->getType(),
                    $parsed->getCondition(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getRecruitmentStatus() : null,
                    $parsed->getMethodType(),
                    $user
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            // dump($e);
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/{metadataId}/update", methods={"POST"}, name="api_update_metadata")
     * @ParamConverter("studyMetadata", options={"mapping": {"metadataId": "id", "studyId": "study"}})
     */
    public function updateMetadata(StudyMetadata $studyMetadata, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $studyMetadata->getStudy());

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var StudyMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);

            $bus->dispatch(
                new UpdateStudyMetadataCommand(
                    $studyMetadata,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getSummary() : null,
                    $parsed->getType(),
                    $parsed->getCondition(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getRecruitmentStatus() : null,
                    $parsed->getMethodType(),
                    $user
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}
