<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\CreateMetadataVersionApiRequest;
use App\Command\Metadata\CreateDatasetMetadataCommand;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DatasetVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata/dataset/{dataset}')]
class DatasetMetadataController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_metadata_dataset_add')]
    public function addDatasetMetadata(
        #[MapEntity(mapping: ['dataset' => 'id'])]
        Dataset $dataset,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DatasetVoter::EDIT, $dataset);

        try {
            $parsed = $this->parseRequest(CreateMetadataVersionApiRequest::class, $request);
            assert($parsed instanceof CreateMetadataVersionApiRequest);

            $this->bus->dispatch(
                new CreateDatasetMetadataCommand(
                    $dataset,
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
                'An error occurred while adding metadata for a dataset',
                [
                    'exception' => $e,
                    'Dataset' => $dataset->getSlug(),
                    'DatasetID' => $dataset->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
