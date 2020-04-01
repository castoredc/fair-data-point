<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Dataset\DatasetsApiResource;
use App\Api\Resource\Distribution\DistributionsApiResource;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Message\Catalog\GetCatalogsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class DatasetApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}", name="api_dataset")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Catalog $catalog, Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if(! $dataset->hasCatalog($catalog))
        {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DatasetApiResource($dataset))->toArray());
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution", name="api_dataset_distributions")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function distributions(Catalog $catalog, Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if(! $dataset->hasCatalog($catalog))
        {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionsApiResource($dataset->getDistributions()->toArray()))->toArray());
    }
}
