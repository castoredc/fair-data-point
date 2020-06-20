<?php
declare(strict_types=1);

namespace App\Api\Controller\Dataset;

use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Distribution\DistributionsApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Dataset;
use App\Service\UriHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DatasetApiController extends ApiController
{
    /**
     * @Route("/api/dataset/{dataset}", name="api_dataset")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        return new JsonResponse((new DatasetApiResource($dataset))->toArray());
    }

    /**
     * @Route("/api/dataset/{dataset}/distribution", methods={"GET"}, name="api_dataset_distributions")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function distributions(Dataset $dataset, UriHelper $uriHelper): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        return new JsonResponse((new DistributionsApiResource($dataset->getDistributions()->toArray(), $uriHelper))->toArray());
    }
}
