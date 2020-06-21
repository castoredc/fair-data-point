<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class BrandApiController extends ApiController
{
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
