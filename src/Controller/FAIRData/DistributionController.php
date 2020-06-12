<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Graph\Resource\Distribution\DistributionGraphResource;
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
    public function distribution(Dataset $dataset, Distribution $distribution, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if ($dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig',
                [
                    'title' => $distribution->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText(),
                    'description' => $distribution->getLatestMetadata()->getDescription()->getTextByLanguageString('en')->getText(),
                ],
            );
        }

        return new Response(
            (new DistributionGraphResource($distribution))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
