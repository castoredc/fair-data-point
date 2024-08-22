<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Catalog;
use App\Security\Authorization\Voter\CatalogVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogController extends AbstractController
{
    /**
     * @Route("/dashboard/catalogs", name="dashboard_catalogs")
     * @Route("/dashboard/catalogs/add", name="dashboard_catalogs_add")
     */
    public function catalogs(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Catalogs']
        );
    }

    /**
     * @Route("/dashboard/catalogs/{catalog}", name="dashboard_catalog")
     * @Route("/dashboard/catalogs/{catalog}/metadata", name="dashboard_catalog_metadata")
     * @Route("/dashboard/catalogs/{catalog}/permissions", name="dashboard_catalog_permissions")
     * @Route("/dashboard/catalogs/{catalog}/datasets", name="dashboard_catalog_datasets")
     * @Route("/dashboard/catalogs/{catalog}/datasets/add", name="dashboard_catalog_dataset_add")
     * @Route("/dashboard/catalogs/{catalog}/studies", name="dashboard_catalog_studies")
     * @Route("/dashboard/catalogs/{catalog}/studies/add", name="dashboard_catalog_study_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted(CatalogVoter::EDIT, $catalog);

        return $this->render('react.html.twig', ['title' => 'FDP Admin']);
    }
}
