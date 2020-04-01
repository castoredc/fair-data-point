<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DistributionController extends FAIRDataController
{
    /**
     * @Route("/fdp/{catalog}/{dataset}/{distribution}", name="distribution")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distribution(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if(! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution))
        {
            throw $this->createNotFoundException();
        }

        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig'
            );
        }

        return new Response(
            $distribution->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
