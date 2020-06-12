<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Request\Study\AddDatasetToCatalogApiRequest;
use App\Api\Request\Study\AddStudyToCatalogApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use App\Message\Catalog\GetCatalogsCommand;
use App\Message\Dataset\AddDatasetToCatalogCommand;
use App\Message\Dataset\GetDatasetCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\FilterStudiesCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use App\Message\Study\GetStudyCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/catalog/{catalog}/dataset")
 * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
 *
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
            /** @var AddDatasetToCatalogApiRequest $parsed */
            $parsed = $this->parseRequest(AddDatasetToCatalogApiRequest::class, $request);

            $envelope = $bus->dispatch(new GetDatasetCommand($parsed->getDatasetId()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Dataset $dataset */
            $dataset = $handledStamp->getResult();

            $this->denyAccessUnlessGranted('edit', $dataset);

            $bus->dispatch(new AddDatasetToCatalogCommand($dataset, $catalog));

            return new JsonResponse((new DatasetApiResource($dataset))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            } elseif ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }
            
            return new JsonResponse([], 500);
        }
    }
}
