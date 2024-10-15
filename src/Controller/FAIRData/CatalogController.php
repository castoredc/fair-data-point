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
    #[Route(path: '/fdp/{catalog}', name: 'redirect_old_catalog')]
    public function catalogRedirect(string $catalog, Request $request): Response
    {
        return $this->redirectToRoute('catalog', [
            'catalog' => $catalog,
            'embed' => $request->get('embed'),
        ], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(path: '/fdp/catalog/{catalog}', name: 'catalog')]
    public function catalog(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return $this->renderResource(
            $request,
            $catalog,
            $bus
        );
    }
}
