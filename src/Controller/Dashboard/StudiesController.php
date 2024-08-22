<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\StudyVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StudiesController extends AbstractController
{
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
     * @Route("/dashboard/studies/add/{catalog}", name="dashboard_study_add_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addStudy(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted(CatalogVoter::ADD, $catalog);

        return $this->render(
            'react.html.twig',
            ['title' => 'Add study']
        );
    }

    /**
     * @Route("/dashboard/studies/{studyId}", name="dashboard_study")
     * @Route("/dashboard/studies/{studyId}/metadata", name="dashboard_study_metadata")
     * @Route("/dashboard/studies/{studyId}/annotations", name="dashboard_study_annotations")
     * @Route("/dashboard/studies/{studyId}/datasets", name="dashboard_study_datasets")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function studyMetadata(Study $study): Response
    {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        return $this->render(
            'react.html.twig',
            ['title' => 'Study']
        );
    }
}
