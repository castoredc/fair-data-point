<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Graph\Resource\Dataset\DatasetGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DatasetController extends FAIRDataController
{
    /**
     * @Route("/fdp/{catalog}/{dataset}", name="dataset")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Catalog $catalog, Dataset $dataset, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if (! $dataset->hasCatalog($catalog)) {
            throw $this->createNotFoundException();
        }

        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig'
            );
        }

        return new Response(
            (new DatasetGraphResource($dataset))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
