<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Message\Catalog\GetCatalogsCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use App\Message\Study\FilterStudiesCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/catalog")
 */
class CatalogApiController extends ApiController
{
    /**
     * @Route("", name="api_catalogs")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCatalogsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    /**
     * @Route("/{catalog}", name="api_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new CatalogApiResource($catalog))->toArray());
    }

    /**
     * @Route("/{catalog}/study", name="api_catalog_studies")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studies(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $catalog,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(StudyApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{catalog}/study/filters", name="api_catalog_studies_filters")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studyFilters(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);
        $studies = $catalog->getStudies($this->isGranted('edit', $catalog));

        return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
    }

    /**
     * @Route("/{catalog}/dataset", name="api_catalog_datasets")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function datasets(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(new GetPaginatedDatasetsCommand(
                $catalog,
                $parsed->getSearch(),
                $parsed->getStudyType(),
                $parsed->getMethodType(),
                $parsed->getCountry(),
                $parsed->getPerPage(),
                $parsed->getPage()
            ));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(DatasetApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{catalog}/map", name="api_catalog_datasets_map")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studiesMap(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(new FilterStudiesCommand($catalog, $parsed->getSearch(), $parsed->getStudyType(), $parsed->getMethodType(), $parsed->getCountry()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new StudiesMapApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}
