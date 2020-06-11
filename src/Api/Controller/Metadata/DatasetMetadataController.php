<?php

namespace App\Api\Controller\Metadata;

use App\Api\Request\Metadata\DatasetMetadataApiRequest;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use App\Message\Metadata\CreateDatasetMetadataCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/metadata/dataset/{dataset}")
 * @ParamConverter("dataset", options={"mapping": {"dataset": "id"}})
 */
class DatasetMetadataController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_metadata_dataset_add")
     */
    public function addCatalogMetadata(Dataset $dataset, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            /** @var DatasetMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(DatasetMetadataApiRequest::class, $request);

            $bus->dispatch(
                new CreateDatasetMetadataCommand(
                    $dataset,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getVersionUpdate()
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