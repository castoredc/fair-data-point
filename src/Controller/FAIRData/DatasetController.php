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
     * @Route("/fdp/dataset/{dataset}", name="dataset")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Catalog $catalog, Dataset $dataset, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        if ($this->acceptsHttp($request)) {
            $metadata = $dataset->getStudy()->getLatestMetadata();

            return $this->render(
                'react.html.twig',
                [
                    'title' => $metadata->getBriefName(),
                    'description' => $metadata->getBriefSummary(),
                ],
            );
        }

        return new Response(
            (new DatasetGraphResource($dataset))->toGraph($this->baseUri)->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
