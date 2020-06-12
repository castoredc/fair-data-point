<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Study;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="admin")
     */
    public function admin(): Response
    {
        return $this->redirectToRoute('admin_catalogs');
    }

    /**
     * @Route("/catalog/{catalog}", name="admin_catalog")
     * @Route("/catalog/{catalog}/metadata", name="admin_catalog_metadata")
     * @Route("/catalog/{catalog}/datasets", name="admin_catalog_datasets")
     * @Route("/catalog/{catalog}/datasets/add", name="admin_catalog_dataset_add")
     * @Route("/catalog/{catalog}/studies", name="admin_catalog_studies")
     * @Route("/catalog/{catalog}/studies/add", name="admin_catalog_study_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function adminCatalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('react.html.twig', ['title' => 'Admin']);
    }

    /**
     * @Route("/study/{studyId}", name="admin_study")
     * @Route("/study/{studyId}/contacts", name="admin_study_contacts")
     * @Route("/study/{studyId}/organizations", name="admin_study_organizations")
     * @Route("/study/{studyId}/consent", name="admin_study_consent")
     * @Route("/study/{studyId}/annotations", name="admin_study_annotations")
     * @Route("/study/{studyId}/datasets", name="admin_study_datasets")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function adminStudy(Study $study): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('react.html.twig', ['title' => 'Admin']);
    }

    /**
     * @Route("/catalogs", name="admin_catalogs")
     * @Route("/models", name="admin_models")
     * @Route("/studies", name="admin_studies")
     */
    public function adminModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/model/{model}", name="admin_model")
     * @Route("/model/{model}/modules", name="admin_model_modules")
     * @Route("/model/{model}/prefixes", name="admin_model_prefixes")
     * @Route("/model/{model}/nodes", name="admin_model_nodes")
     * @Route("/model/{model}/preview", name="admin_model_preview")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/catalog/{catalog}/dataset/{dataset}", name="admin_catalog_dataset")
     * @Route("/catalog/{catalog}/dataset/{dataset}/metadata", name="admin_catalog_dataset_metadata")
     * @Route("/catalog/{catalog}/dataset/{dataset}/distributions", name="admin_catalog_dataset_distributions")
     * @Route("/catalog/{catalog}/dataset/{dataset}/distributions/add", name="admin_catalog_dataset_distribution_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function adminCatalogDataset(Catalog $catalog, Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/dataset/{dataset}", name="admin_dataset")
     * @Route("/dataset/{dataset}/metadata", name="admin_dataset_metadata")
     * @Route("/dataset/{dataset}/distributions", name="admin_dataset_distributions")
     * @Route("/dataset/{dataset}/distributions/add", name="admin_dataset_distribution_add")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function adminDataset(Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}", name="admin_study_distribution")
     * @Route("/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/metadata", name="admin_study_distribution_metadata")
     * @Route("/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/contents", name="admin_study_distribution_content")
     * @Route("/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/prefixes", name="admin_study_distribution_prefix")
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
