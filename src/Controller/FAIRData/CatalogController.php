<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Catalog;
use App\Graph\Resource\Catalog\CatalogGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends FAIRDataController
{
    /**
     * @Route("/fdp/{catalog}", name="catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalog(Catalog $catalog, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig',
                [
                    'title' => $catalog->getTitle()->getTextByLanguageString('en')->getText(),
                    'description' => $catalog->getDescription()->getTextByLanguageString('en')->getText(),
                ],
            );
        }

        return new Response(
            (new CatalogGraphResource($catalog))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
