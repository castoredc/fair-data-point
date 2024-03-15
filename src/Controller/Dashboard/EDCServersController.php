<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EDCServersController extends AbstractController
{
    /** @Route("/dashboard/edc-servers", name="dashboard_edcservers") */
    public function edcServers(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'EDC Servers']
        );
    }
}
