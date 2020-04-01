<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\FAIRData\Catalog;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function strtr;
use function urlencode;

class CastorAuthenticator extends SocialAuthenticator
{
    /** @var ClientRegistry */
    private $clientRegistry;

    /** @var EntityManagerInterface */
    private $em;

    /** @var RouterInterface */
    private $router;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient, ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->apiClient = $apiClient;
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_castor_check';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getCastorClient());
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var CastorUser $castorUser */
        $castorUser = $this->getCastorClient()
            ->fetchUserFromToken($credentials);

        /** @var CastorUser|null $user */
        $user = $this->em->getRepository(CastorUser::class)
            ->findOneBy(['id' => $castorUser->getId()]);

        if ($user === null) {
            $user = $castorUser;
        } else {
            $user->setToken($castorUser->getToken());
        }

        $this->apiClient->setToken($user->getToken());

        $user->setStudies($this->apiClient->getStudyIds());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function getCastorClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('castor');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        $url = $this->router->generate('fdp');
        $previous = $request->getSession()->get('previous');

        if ($previous !== null) {
            $url = $previous;
        }

        return new RedirectResponse($url);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        $url = '/login';

        if ($request->attributes->has('catalog') && $request->attributes->get('catalog') instanceof Catalog) {
            /** @var Catalog $catalog */
            $catalog = $request->attributes->get('catalog');

            if ($catalog->isAcceptingSubmissions()) {
                $url .= '/' . $catalog->getSlug();
            }
        }

        return new RedirectResponse(
            $url . '?path=' . urlencode($request->getRequestUri()), // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
