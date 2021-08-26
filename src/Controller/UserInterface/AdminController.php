<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Data\DataDictionary\DataDictionary;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;
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

        return $this->render('react.html.twig', ['title' => 'FDP Admin']);
    }

    /**
     * @Route("/study/{studyId}", name="admin_study")
     * @Route("/study/{studyId}/metadata", name="admin_study_metadata")
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

        return $this->render('react.html.twig', ['title' => 'FDP Admin']);
    }

    /**
     * @Route("/fdp/metadata", name="admin_fdp_metadata")
     * @Route("/catalogs", name="admin_catalogs")
     * @Route("/models", name="admin_models")
     * @Route("/studies", name="admin_studies")
     * @Route("/datasets", name="admin_datasets")
     * @Route("/dictionaries", name="admin_dictionaries")
     */
    public function adminModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/model/{model}", name="admin_model")
     * @Route("/model/{model}/versions", name="admin_model_versions")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/dictionary/{dataDicationary}", name="admin_dictionary")
     * @Route("/dictionary/{dataDicationary}/versions", name="admin_dictionary_versions")
     * @ParamConverter("dataDicationary", options={"mapping": {"dataDicationary": "id"}})
     */
    public function adminDataDictionary(DataDictionary $dataDicationary): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/model/{model}/{version}/modules", name="admin_model_modules")
     * @Route("/model/{model}/{version}/prefixes", name="admin_model_prefixes")
     * @Route("/model/{model}/{version}/nodes", name="admin_model_nodes")
     * @Route("/model/{model}/{version}/preview", name="admin_model_preview")
     * @Route("/model/{model}/{version}/import-export", name="admin_model_importexport")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersion(DataModel $dataModel, DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
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
            ['title' => 'FDP Admin']
        );
    }
}
