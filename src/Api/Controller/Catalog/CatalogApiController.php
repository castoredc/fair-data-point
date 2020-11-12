<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\CatalogApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Command\Catalog\UpdateCatalogCommand;
use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Command\Study\FilterStudiesCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
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
 * @Route("/api/catalog/{catalog}")
 * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
 */
class CatalogApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_catalog")
     */
    public function catalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new CatalogApiResource($catalog))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_catalog_update")
     */
    public function updateCatalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $catalog);

        try {
            $parsed = $this->parseRequest(CatalogApiRequest::class, $request);
            assert($parsed instanceof CatalogApiRequest);

            $bus->dispatch(
                new UpdateCatalogCommand($catalog, $parsed->getSlug(), $parsed->isAcceptSubmissions(), $parsed->isSubmissionAccessesData())
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating the catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/dataset", name="api_catalog_datasets")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function datasets(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(new GetPaginatedDatasetsCommand(
                $catalog,
                $parsed->getSearch(),
                $parsed->getStudyType(),
                $parsed->getMethodType(),
                $parsed->getCountry(),
                null,
                $parsed->getPerPage(),
                $parsed->getPage()
            ));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(DatasetApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the datasets for a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/map", name="api_catalog_datasets_map")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studiesMap(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(new FilterStudiesCommand($catalog, $parsed->getSearch(), $parsed->getStudyType(), $parsed->getMethodType(), $parsed->getCountry()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new StudiesMapApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the map information for a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}
