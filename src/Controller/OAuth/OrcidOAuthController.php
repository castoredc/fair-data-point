<?php
declare(strict_types=1);

namespace App\Controller\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrcidOAuthController extends AbstractController
{
    /**
     * @Route("/oauth/connect/orcid", name="oauth_orcid_start")
     * @ParamConverter("server", options={"mapping": {"server": "id"}})
     */
    public function connectAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        $request->getSession()->set('previous', $request->get('target_path'));

        return $clientRegistry
            ->getClient('orcid')
            ->redirect([], []);
    }

    /** @Route("/oauth/check/orcid", name="oauth_orcid_check") */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): void
    {
    }
}
