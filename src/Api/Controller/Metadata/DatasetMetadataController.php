<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\DatasetMetadataApiRequest;
use App\Command\Metadata\CreateDatasetMetadataCommand;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata/dataset/{dataset}")
 * @ParamConverter("dataset", options={"mapping": {"dataset": "id"}})
 */
class DatasetMetadataController extends ApiController
{
    /** @Route("", methods={"POST"}, name="api_metadata_dataset_add") */
    public function addDatasetMetadata(Dataset $dataset, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            $parsed = $this->parseRequest(DatasetMetadataApiRequest::class, $request);
            assert($parsed instanceof DatasetMetadataApiRequest);

            $bus->dispatch(
                new CreateDatasetMetadataCommand(
                    $dataset,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getVersionUpdate(),
                    $parsed->getPublishers(),
                    $parsed->getTheme(),
                    $parsed->getKeyword()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata for a dataset', [
                'exception' => $e,
                'Dataset' => $dataset->getSlug(),
                'DatasetID' => $dataset->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
