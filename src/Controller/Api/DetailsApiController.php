<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Metadata\StudyMetadataApiRequest;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Message\Metadata\CreateStudyMetadataCommand;
use App\Message\Metadata\UpdateStudyMetadataCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
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

        if (! $study->hasMetadata()) {
            return new JsonResponse([]);
        }

        return new JsonResponse((new StudyMetadataApiResource($study->getLatestMetadata()))->toArray());
    }

    /**
     * @Route("/api/study/{studyId}/metadata", methods={"POST"}, name="api_add_metadata")
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
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/{metadataId}", methods={"POST"}, name="api_update_metadata")
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
