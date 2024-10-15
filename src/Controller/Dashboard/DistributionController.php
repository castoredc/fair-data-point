<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\Authorization\Voter\DistributionVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DistributionController extends AbstractController
{
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}', name: 'dashboard_study_dataset_distribution')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/metadata', name: 'dashboard_study_distribution_metadata')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/permissions', name: 'dashboard_study_distribution_permissions')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/contents', name: 'dashboard_study_distribution_content')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log', name: 'dashboard_study_distribution_log')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/subset', name: 'dashboard_study_distribution_subset')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}', name: 'dashboard_catalog_dataset_distribution')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/metadata', name: 'dashboard_catalog_distribution_metadata')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/permissions', name: 'dashboard_catalog_distribution_permissions')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/contents', name: 'dashboard_catalog_distribution_content')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log', name: 'dashboard_catalog_distribution_log')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/subset', name: 'dashboard_catalog_distribution_subset')]
    public function distribution(#[MapEntity(mapping: ['dataset' => 'slug'])]
    Dataset $dataset, #[MapEntity(mapping: ['distribution' => 'slug'])]
    Distribution $distribution,): Response
    {
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }

    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/{distribution}/log/{log}', name: 'dashboard_study_distribution_log_records')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/{distribution}/log/{log}', name: 'dashboard_catalog_distribution_log_records')]
    public function adminDistributionLogRecords(#[MapEntity(mapping: ['dataset' => 'slug'])]
    Dataset $dataset, #[MapEntity(mapping: ['distribution' => 'slug'])]
    Distribution $distribution, #[MapEntity(mapping: ['log' => 'id'])]
    DistributionGenerationLog $log,): Response
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
