<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    /**
     * @return RedirectResponse
     *
     * @Route("/dashboard", name="redirect_dashboard")
     */
    public function redirectToStudies(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->redirectToRoute('dashboard_studies');
    }

    /**
     * @Route("/dashboard/studies", name="dashboard_studies")
     * @Route("/dashboard/studies/add", name="dashboard_study_add")
     */
    public function studies(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Studies']
        );
    }

    /** @Route("/dashboard/data-models", name="dashboard_data_models") */
    public function dataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /** @Route("/dashboard/data-models/add", name="dashboard_data_model_add") */
    public function addDataModel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /** @Route("/dashboard/fdp", name="dashboard_fdp") */
    public function fdp(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FAIR Data Point']
        );
    }

    /** @Route("/dashboard/edc-servers", name="dashboard_edcservers") */
    public function edcServers(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'EDC Servers']
        );
    }

    /**
     * @Route("/dashboard/studies/add/{catalog}", name="dashboard_study_add_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addStudy(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

        return $this->render(
            'react.html.twig',
            ['title' => 'Add study']
        );
    }

    /**
     * @Route("/dashboard/studies/{studyId}", name="dashboard_study")
     * @Route("/dashboard/studies/{studyId}/team", name="dashboard_study_metadata_team")
     * @Route("/dashboard/studies/{studyId}/centers", name="dashboard_study_metadata_centers")
     * @Route("/dashboard/studies/{studyId}/annotations", name="dashboard_study_annotations")
     * @Route("/dashboard/studies/{studyId}/datasets", name="dashboard_study_datasets")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function studyMetadata(Study $study): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        return $this->render(
            'react.html.twig',
            ['title' => 'Study']
        );
    }

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
        $this->denyAccessUnlessGranted('edit', $catalog);

        return $this->render('react.html.twig', ['title' => 'FDP Admin']);
    }

    /**
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}", name="dashboard_study_dataset")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/metadata", name="dashboard_study_dataset_metadata")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/permissions", name="dashboard_study_dataset_permissions")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions", name="dashboard_study_dataset_distributions")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/add", name="dashboard_study_dataset_distributions_add")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}", name="dashboard_catalog_dataset")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/metadata", name="dashboard_catalog_dataset_metadata")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/permissions", name="dashboard_catalog_dataset_permissions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions", name="dashboard_catalog_dataset_distributions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/add", name="dashboard_catalog_dataset_distributions_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }

    /**
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}", name="dashboard_study_dataset_distribution")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/metadata", name="dashboard_study_distribution_metadata")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/permissions", name="dashboard_study_distribution_permissions")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/contents", name="dashboard_study_distribution_content")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log", name="dashboard_study_distribution_log")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/subset", name="dashboard_study_distribution_subset")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}", name="dashboard_catalog_dataset_distribution")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/metadata", name="dashboard_catalog_distribution_metadata")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/permissions", name="dashboard_catalog_distribution_permissions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/contents", name="dashboard_catalog_distribution_content")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log", name="dashboard_catalog_distribution_log")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/subset", name="dashboard_catalog_distribution_subset")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distribution(Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }

    /**
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log/{log}", name="dashboard_study_distribution_log_records")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log/{log}", name="dashboard_catalog_distribution_log_records")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     * @ParamConverter("log", options={"mapping": {"log": "id"}})
     */
    public function adminDistributionLogRecords(Dataset $dataset, Distribution $distribution, DistributionGenerationLog $log): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution) || $log->getDistribution()->getDistribution() !== $distribution) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}", name="dashboard_model")
     * @Route("/dashboard/data-models/{model}/versions", name="dashboard_model_versions")
     * @Route("/dashboard/data-models/{model}/permissions", name="dashboard_model_permissions")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => $dataModel->getTitle()]
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}/{version}/modules", name="dashboard_model_modules")
     * @Route("/dashboard/data-models/{model}/{version}/prefixes", name="dashboard_model_prefixes")
     * @Route("/dashboard/data-models/{model}/{version}/preview", name="dashboard_model_preview")
     * @Route("/dashboard/data-models/{model}/{version}/import-export", name="dashboard_model_importexport")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersion(DataModel $dataModel, DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}/{version}/nodes/{nodeType}", name="dashboard_model_nodes")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersionNodes(DataModel $dataModel, DataModelVersion $dataModelVersion, string $nodeType): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
