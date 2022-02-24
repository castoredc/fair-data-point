<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\AddDatasetToCatalogApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Command\Dataset\AddDatasetToCatalogCommand;
use App\Command\Dataset\GetDatasetCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/catalog/{catalog}/dataset")
 * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
 */
class CatalogDatasetsApiController extends ApiController
{
    /**
     * @Route("/add", methods={"POST"}, name="api_add_dataset_to_catalog")
     */
    public function addDatasetToCatalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

        try {
            $parsed = $this->parseRequest(AddDatasetToCatalogApiRequest::class, $request);
            assert($parsed instanceof AddDatasetToCatalogApiRequest);

            $envelope = $bus->dispatch(new GetDatasetCommand($parsed->getDatasetId()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $dataset = $handledStamp->getResult();
            assert($dataset instanceof Dataset);

            $this->denyAccessUnlessGranted('edit', $dataset);

            $bus->dispatch(new AddDatasetToCatalogCommand($dataset, $catalog));

            return new JsonResponse((new DatasetApiResource($dataset))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            $this->logger->critical('An error occurred while adding a dataset to a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
