<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\CatalogApiResource;
use App\Api\Resource\CatalogBrandApiResource;
use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CatalogApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}", name="api_catalogs")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function studies(Catalog $catalog, MessageBusInterface $bus): Response
    {
        return new JsonResponse((new CatalogApiResource($catalog))->toArray());
    }

    /**
     * @Route("/api/brand/{catalog}", name="api_catalog_brand")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function brand(Catalog $catalog, MessageBusInterface $bus): Response
    {
        return new JsonResponse((new CatalogBrandApiResource($catalog))->toArray());
    }
}
