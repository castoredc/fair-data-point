<?php
declare(strict_types=1);

namespace App\Controller\Wizard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserOnboardingWizardController extends AbstractController
{
    /**
     * @Route("/wizard/onboarding", name="wizard_user_onboarding")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('react.html.twig');
    }
}
