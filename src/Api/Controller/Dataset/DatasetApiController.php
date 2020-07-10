<?php
declare(strict_types=1);

namespace App\Api\Controller\Dataset;

use App\Api\Request\Dataset\DatasetApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Distribution\DistributionsApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use App\Message\Dataset\UpdateDatasetCommand;
use App\Service\UriHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/dataset/{dataset}")
 * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
 */
class DatasetApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_dataset")
     */
    public function dataset(Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        return new JsonResponse((new DatasetApiResource($dataset))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_dataset_update")
     */
    public function updateDataset(Dataset $dataset, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            /** @var DatasetApiRequest $parsed */
            $parsed = $this->parseRequest(DatasetApiRequest::class, $request);
            $bus->dispatch(
                new UpdateDatasetCommand(
                    $dataset,
                    $parsed->getSlug(),
                    $parsed->getPublished()
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a dataset', [
                'exception' => $e,
                'Dataset' => $dataset->getSlug(),
                'DatasetID' => $dataset->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/distribution", methods={"GET"}, name="api_dataset_distributions")
     */
    public function distributions(Dataset $dataset, UriHelper $uriHelper): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        return new JsonResponse((new DistributionsApiResource($dataset->getDistributions()->toArray(), $uriHelper))->toArray());
    }
}
