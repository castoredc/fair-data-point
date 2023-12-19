<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @return RedirectResponse
     *
     * @Route("/", name="redirect_homepage")
     */
    public function redirectToIndex(): Response
    {
        return $this->redirectToRoute('fdp');
    }
}
