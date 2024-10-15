<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class BrandApiController extends ApiController
{
    #[Route(path: '/api/brand/{catalog}', name: 'api_catalog_brand')]
    public function brand(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return new JsonResponse((new CatalogBrandApiResource($catalog))->toArray());
    }
}
