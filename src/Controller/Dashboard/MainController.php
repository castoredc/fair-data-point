<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MainController extends AbstractController
{
    /**
     * @return RedirectResponse
     */
    #[Route(path: '/dashboard', name: 'redirect_dashboard')]
    public function redirectToStudies(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->redirectToRoute('dashboard_studies');
    }
}
