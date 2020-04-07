<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Dataset\DatasetApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Api\Resource\Dataset\DatasetsFilterApiResource;
use App\Api\Resource\Dataset\DatasetsMapApiResource;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Message\Catalog\GetCatalogsCommand;
use App\Message\Dataset\GetAdminPaginatedDatasetsCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CatalogApiController extends ApiController
{
    /**
     * @Route("/api/catalog", name="api_catalogs")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCatalogsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    /**
     * @Route("/api/catalog/{catalog}", name="api_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studies(Catalog $catalog, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new CatalogApiResource($catalog))->toArray());
    }

    /**
     * @Route("/api/catalog/{catalog}/filters", name="api_catalog_filters")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studyFilters(Catalog $catalog, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            if ($this->isGranted('ROLE_ADMIN')) {
                $datasets = $catalog->getDatasets($this->isGranted('edit', $catalog))->toArray();
            } else {
                $envelope = $bus->dispatch(new GetDatasetsCommand($catalog, null, null, null, null));
                /** @var HandledStamp $handledStamp */
                $handledStamp = $envelope->last(HandledStamp::class);
                $datasets = $handledStamp->getResult();
            }

            return new JsonResponse((new DatasetsFilterApiResource($datasets))->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset", name="api_catalog_datasets")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function datasets(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var DatasetApiRequest $parsed */
            $parsed = $this->parseRequest(DatasetApiRequest::class, $request);

            if ($parsed->isAdmin() && $this->isGranted('ROLE_ADMIN')) {
                $command = new GetAdminPaginatedDatasetsCommand(
                    $catalog,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                );
            } else {
                $command = new GetPaginatedDatasetsCommand(
                    $catalog,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                );
            }

            $envelope = $bus->dispatch($command);

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/map", name="api_catalog_datasets_map")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function datasetsMap(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var DatasetApiRequest $parsed */
            $parsed = $this->parseRequest(DatasetApiRequest::class, $request);

            $envelope = $bus->dispatch(new GetDatasetsCommand($catalog, $parsed->getSearch(), $parsed->getStudyType(), $parsed->getMethodType(), $parsed->getCountry()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new DatasetsMapApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/brand/{catalog}", name="api_catalog_brand")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function brand(Catalog $catalog, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new CatalogBrandApiResource($catalog))->toArray());
    }
}
