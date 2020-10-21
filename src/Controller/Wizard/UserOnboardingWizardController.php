<?php
declare(strict_types=1);

namespace App\Controller\Wizard;

use App\Entity\Enum\Wizard;
use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function urlencode;

class UserOnboardingWizardController extends AbstractController
{
    /**
     * @Route("/wizard/user/details", name="wizard_user_details")
     */
    public function details(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->getResponse($request);
    }

    /**
     * @Route("/wizard/user/affiliations", name="wizard_user_affiliations")
     */
    public function affiliations(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->getResponse($request);
    }

    private function getResponse(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        if ($user->getWizards()->isEmpty()) {
            return $this->redirectToRoute('fdp');
        }

        $firstWizard = $user->getWizards()->first();
        assert($firstWizard instanceof Wizard);
        $currentRoute = $request->get('_route');

        if ($firstWizard->getRoute() !== $currentRoute) {
            return $this->redirectToRoute($firstWizard->getRoute(), [
                'origin' => urlencode($request->get('origin')),
            ]);
        }

        return $this->render('react.html.twig');
    }
}
