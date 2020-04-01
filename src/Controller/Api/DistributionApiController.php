<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Dataset\DatasetsApiResource;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionsApiResource;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Message\Catalog\GetCatalogsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class DistributionApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}", name="api_distribution")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function dataset(Catalog $catalog, Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if(! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution))
        {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionApiResource($distribution))->toArray());
    }
}
