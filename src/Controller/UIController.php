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
     * @Route("/my-studies/study/add", name="add_study")
     * @Route("/my-studies/study/{studyId}/metadata/details", name="study_metadata_details")
     * @Route("/my-studies/study/{studyId}/metadata/organizations", name="study_metadata_organization")
     * @Route("/my-studies/study/{studyId}/metadata/contacts", name="study_metadata_contact")
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
