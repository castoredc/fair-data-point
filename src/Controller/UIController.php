<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UIController extends AbstractController
{
    /**
     * @return RedirectResponse
     *
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('fdp_render');
    }

    /**
     * @Route("/login", name="login")
     * @Route("/my-studies", name="my_studies")
     * @Route("/my-studies/{catalog}/study/add", name="add_study")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/details", name="study_metadata_details")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/centers", name="study_metadata_centers")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/contacts", name="study_metadata_contact")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/finished", name="study_metadata_finished")
     */
    public function react(): Response
    {
        return $this->render(
            'react.html.twig'
        );
    }

    /**
     * @Route("/redirect-login", name="redirect_login")
     */
    public function loginRedirect(Request $request): Response
    {
        return $this->redirectToRoute('fdp_render');
    }
}
