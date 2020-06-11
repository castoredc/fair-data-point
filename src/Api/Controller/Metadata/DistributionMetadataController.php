<?php

namespace App\Api\Controller\Metadata;

use App\Api\Request\Metadata\DatasetMetadataApiRequest;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Message\Metadata\CreateDatasetMetadataCommand;
use App\Message\Metadata\CreateDistributionMetadataCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/metadata/distribution/{distribution}")
 * @ParamConverter("distribution", options={"mapping": {"distribution": "id"}})
 */
class DistributionMetadataController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_metadata_distribution_add")
     */
    public function addCatalogMetadata(Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        try {
            /** @var DatasetMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(DatasetMetadataApiRequest::class, $request);

            $bus->dispatch(
                new CreateDistributionMetadataCommand(
                    $distribution,
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