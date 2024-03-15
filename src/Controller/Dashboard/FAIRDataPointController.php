<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FAIRDataPointController extends AbstractController
{
    /** @Route("/dashboard/fdp", name="dashboard_fdp") */
    public function fdp(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FAIR Data Point']
        );
    }
}
