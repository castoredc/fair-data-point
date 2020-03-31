<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\CatalogApiResource;
use App\Api\Resource\CatalogBrandApiResource;
use App\Api\Resource\DatasetsApiResource;
use App\Entity\FAIRData\Catalog;
use App\Message\Api\Study\GetCatalogsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/api/catalog/{catalog}/dataset", name="api_catalog_datasets")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function datasets(Catalog $catalog, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new DatasetsApiResource($catalog->getDatasets()->toArray()))->toArray());
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
