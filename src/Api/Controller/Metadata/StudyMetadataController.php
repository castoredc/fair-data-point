<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\CreateMetadataVersionApiRequest;
use App\Command\Metadata\CreateStudyMetadataCommand;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\StudyVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata/study/{study}')]
class StudyMetadataController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_metadata_study_add')]
    public function addStudyMetadata(
        #[MapEntity(mapping: ['study' => 'id'])]
        Study $study,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        try {
            $parsed = $this->parseRequest(CreateMetadataVersionApiRequest::class, $request);
            assert($parsed instanceof CreateMetadataVersionApiRequest);

            $this->bus->dispatch(
                new CreateStudyMetadataCommand(
                    $study,
                    $parsed->getVersionType(),
                    $parsed->getModel(),
                    $parsed->getModelVersion(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while adding metadata for a study',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
