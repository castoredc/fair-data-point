<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Dataset;
use App\Security\Authorization\Voter\DatasetVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DatasetController extends AbstractController
{
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}', name: 'dashboard_study_dataset')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/metadata', name: 'dashboard_study_dataset_metadata')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/permissions', name: 'dashboard_study_dataset_permissions')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions', name: 'dashboard_study_dataset_distributions')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets/{dataset}/distributions/add', name: 'dashboard_study_dataset_distributions_add')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}', name: 'dashboard_catalog_dataset')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/metadata', name: 'dashboard_catalog_dataset_metadata')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/permissions', name: 'dashboard_catalog_dataset_permissions')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions', name: 'dashboard_catalog_dataset_distributions')]
    #[Route(path: '/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/add', name: 'dashboard_catalog_dataset_distributions_add')]
    public function dataset(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
    ): Response {
        $this->denyAccessUnlessGranted(DatasetVoter::EDIT, $dataset);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }
}
