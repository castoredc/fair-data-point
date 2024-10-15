<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\StudyVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StudiesController extends AbstractController
{
    #[Route(path: '/dashboard/studies', name: 'dashboard_studies')]
    #[Route(path: '/dashboard/studies/add', name: 'dashboard_study_add')]
    public function studies(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Studies']
        );
    }

    #[Route(path: '/dashboard/studies/add/{catalog}', name: 'dashboard_study_add_catalog')]
    public function addStudy(
        #[MapEntity(mapping: ['catalog' => 'slug'])]
        Catalog $catalog,
    ): Response {
        $this->denyAccessUnlessGranted(CatalogVoter::ADD, $catalog);

        return $this->render(
            'react.html.twig',
            ['title' => 'Add study']
        );
    }

    #[Route(path: '/dashboard/studies/{studyId}', name: 'dashboard_study')]
    #[Route(path: '/dashboard/studies/{studyId}/metadata', name: 'dashboard_study_metadata')]
    #[Route(path: '/dashboard/studies/{studyId}/annotations', name: 'dashboard_study_annotations')]
    #[Route(path: '/dashboard/studies/{studyId}/datasets', name: 'dashboard_study_datasets')]
    public function studyMetadata(
        #[MapEntity(mapping: ['studyId' => 'id'])]
        Study $study,
    ): Response {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        return $this->render(
            'react.html.twig',
            ['title' => 'Study']
        );
    }
}
