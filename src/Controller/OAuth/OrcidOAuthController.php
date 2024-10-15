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
    #[Route(path: '/oauth/connect/orcid', name: 'oauth_orcid_start')]
    public function connect(Request $request, ClientRegistry $clientRegistry): Response
    {
        $request->getSession()->set('previous', $request->get('target_path'));

        return $clientRegistry
            ->getClient('orcid')
            ->redirect([], []);
    }

    #[Route(path: '/oauth/check/orcid', name: 'oauth_orcid_check')]
    public function connectCheck(ClientRegistry $clientRegistry): void
    {
    }
}
