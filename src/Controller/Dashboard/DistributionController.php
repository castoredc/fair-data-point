<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\Authorization\Voter\DistributionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DistributionController extends AbstractController
{
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
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

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
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

        if (! $dataset->hasDistribution($distribution) || $log->getDistribution()->getDistribution() !== $distribution) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
