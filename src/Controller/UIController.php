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
     * @Route("/redirect-login", name="redirect_login")
     */
    public function loginRedirect(Request $request): Response
    {
        return $this->redirectToRoute('fdp_render');
    }
}
