<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Catalog;
use App\Security\Authorization\Voter\CatalogVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogController extends AbstractController
{
    #[Route(path: '/dashboard/catalogs', name: 'dashboard_catalogs')]
    #[Route(path: '/dashboard/catalogs/add', name: 'dashboard_catalogs_add')]
    public function catalogs(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Catalogs']
        );
    }

    #[Route(path: '/dashboard/catalogs/{catalog}', name: 'dashboard_catalog')]
    #[Route(path: '/dashboard/catalogs/{catalog}/metadata', name: 'dashboard_catalog_metadata')]
    #[Route(path: '/dashboard/catalogs/{catalog}/permissions', name: 'dashboard_catalog_permissions')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets', name: 'dashboard_catalog_datasets')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/add', name: 'dashboard_catalog_dataset_add')]
    #[Route(path: '/dashboard/catalogs/{catalog}/studies', name: 'dashboard_catalog_studies')]
    #[Route(path: '/dashboard/catalogs/{catalog}/studies/add', name: 'dashboard_catalog_study_add')]
    public function catalog(
        #[MapEntity(mapping: ['catalog' => 'slug'])]
        Catalog $catalog,
    ): Response {
        $this->denyAccessUnlessGranted(CatalogVoter::EDIT, $catalog);

        return $this->render('react.html.twig', ['title' => 'FDP Admin']);
    }
}
