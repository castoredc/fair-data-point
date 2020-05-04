<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin | Catalogs']
        );
    }

    /**
     * @Route("/admin/catalog/{catalog}", name="admin_catalog")
     * @Route("/admin/catalog/{catalog}/studies", name="admin_catalog_studies")
     * @Route("/admin/catalog/{catalog}/studies/add", name="admin_catalog_study_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function adminCatalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin | ' . $catalog->getTitle()->getTextByLanguageString('en')->getText()]
        );
    }

    /**
     * @Route("/admin/study/{studyId}", name="admin_study")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function adminStudy(Catalog $catalog, Study $study): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}", name="admin_dataset")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/contacts", name="admin_dataset_contacts")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/organizations", name="admin_dataset_organizations")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/consent", name="admin_dataset_consent")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/distributions", name="admin_dataset_distributions")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/distributions/add", name="admin_dataset_distribution_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function adminDataset(Catalog $catalog, Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}", name="admin_study_distribution")
     * @Route("/admin/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/contents", name="admin_study_distribution_content")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function adminDistribution(Catalog $catalog, Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }
}
