<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @return RedirectResponse
     *
     * @Route("/dashboard", name="redirect_dashboard")
     */
    public function redirectToStudies(Request $request): Response
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

    /**
     * @Route("/dashboard/data-models", name="dashboard_data_models")
     * @Route("/dashboard/data-models/add", name="dashboard_data_model_add")
     */
    public function dataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /**
     * @Route("/dashboard/fdp", name="dashboard_fdp")
     */
    public function fdp(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FAIR Data Point']
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
            ['title' => $catalog->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText() . ' | Add study']
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
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}", name="dashboard_study_dataset")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/metadata", name="dashboard_study_dataset_metadata")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions", name="dashboard_study_dataset_distributions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}", name="dashboard_catalog_dataset")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/metadata", name="dashboard_catalog_dataset_metadata")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions", name="dashboard_catalog_dataset_distributions")
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
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}", name="dashboard_dataset_distribution")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/metadata", name="admin_study_distribution_metadata")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/contents", name="admin_study_distribution_content")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log", name="admin_study_distribution_log")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/subset", name="admin_study_distribution_subset")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}", name="dashboard_dataset_distribution")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/metadata", name="admin_study_distribution_metadata")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/contents", name="admin_study_distribution_content")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log", name="admin_study_distribution_log")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/subset", name="admin_study_distribution_subset")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distribution(Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }

    /**
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log/{log}", name="admin_study_distribution_log_records")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log/{log}", name="admin_catalog_distribution_log_records")
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

//    /**
//     * @Route("/catalog/{catalog}/dataset/{dataset}", name="admin_catalog_dataset")
//     * @Route("/catalog/{catalog}/dataset/{dataset}/metadata", name="admin_catalog_dataset_metadata")
//     * @Route("/catalog/{catalog}/dataset/{dataset}/distributions", name="admin_catalog_dataset_distributions")
//     * @Route("/catalog/{catalog}/dataset/{dataset}/distributions/add", name="admin_catalog_dataset_distribution_add")
//     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
//     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
//     */
//    public function dataset(Catalog $catalog, Dataset $dataset): Response
//    {
//        $this->denyAccessUnlessGranted('edit', $dataset);
//
//        return $this->render(
//            'react.html.twig',
//            ['title' => 'FDP Admin']
//        );
//    }
}
