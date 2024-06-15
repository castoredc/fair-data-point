<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends FAIRDataController
{
    /** @Route("/fdp/{catalog}", name="redirect_old_catalog") */
    public function catalogRedirect(string $catalog, Request $request): Response
    {
        return $this->redirectToRoute('catalog', [
            'catalog' => $catalog,
            'embed' => $request->get('embed'),
        ], Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @Route("/fdp/catalog/{catalog}", name="catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return $this->renderResource(
            $request,
            $catalog,
            $bus
        );
    }
}
