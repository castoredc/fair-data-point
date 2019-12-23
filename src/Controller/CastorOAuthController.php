<?php
declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CastorOAuthController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/castor", name="connect_castor_start")
     */
    public function connectAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        $request->getSession()->set('previous', $request->get('target_path'));

        return $clientRegistry
            ->getClient('castor')
            ->redirect([], []);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/login/check-castor", name="connect_castor_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): void
    {
    }
}
