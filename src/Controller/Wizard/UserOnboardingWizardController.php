<?php
declare(strict_types=1);

namespace App\Controller\Wizard;

use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function count;

class UserOnboardingWizardController extends AbstractController
{
    /**
     * @Route("/wizard/onboarding", name="wizard_user_onboarding")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        if (count($user->getWizards()) === 0) {
            return $this->redirectToRoute('fdp');
        }

        return $this->render('react.html.twig');
    }
}
