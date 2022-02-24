<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\StudyMetadataApiRequest;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Command\Metadata\CreateStudyMetadataCommand;
use App\Command\Metadata\UpdateStudyMetadataCommand;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class StudyMetadataApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/metadata", methods={"GET"}, name="api_get_metadata")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getMetadata(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

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

        try {
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataApiRequest);

            $bus->dispatch(
                new CreateStudyMetadataCommand(
                    $study,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getSummary() : null,
                    $parsed->getType(),
                    $parsed->getConditions(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getRecruitmentStatus() : null,
                    $parsed->getMethodType(),
                    $parsed->getKeywords()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata to a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/study/{studyId}/metadata/{metadataId}", methods={"POST"}, name="api_update_metadata")
     * @ParamConverter("studyMetadata", options={"mapping": {"metadataId": "id", "studyId": "study"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function updateMetadata(Study $study, StudyMetadata $studyMetadata, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $studyMetadata->getStudy());

        try {
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataApiRequest);

            $bus->dispatch(
                new UpdateStudyMetadataCommand(
                    $studyMetadata,
                    $parsed->getBriefName(),
                    $parsed->getScientificName(),
                    $parsed->getBriefSummary(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getSummary() : null,
                    $parsed->getType(),
                    $parsed->getConditions(),
                    $parsed->getIntervention(),
                    $parsed->getEstimatedEnrollment(),
                    $parsed->getEstimatedStudyStartDate(),
                    $parsed->getEstimatedStudyCompletionDate(),
                    $this->isGranted('ROLE_ADMIN') ? $parsed->getRecruitmentStatus() : null,
                    $parsed->getMethodType(),
                    $parsed->getKeywords()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating metadata from a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
                'StudyMetadata' => $studyMetadata->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
