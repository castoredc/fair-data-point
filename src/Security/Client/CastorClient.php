<?php

namespace App\Security\Client;

use App\Security\CastorUserProvider;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CastorClient extends OAuth2Client
{
    const OAUTH2_SESSION_STATE_KEY = 'knpu.oauth2_client_state';
    const SESSION_SERVER_KEY = 'castor.server';

    /** @var CastorUserProvider */
    private $provider;

    /** @var RequestStack */
    private $requestStack;

    /**
     * OAuth2Client constructor.
     */
    public function __construct(CastorUserProvider $provider, RequestStack $requestStack)
    {
        parent::__construct($provider, $requestStack);

        $this->provider = $provider;
        $this->requestStack = $requestStack;
    }

    /**
     * Creates a RedirectResponse that will send the user to the
     * OAuth2 server (e.g. send them to Facebook).
     *
     * @param array $scopes  The scopes you want (leave empty to use default)
     * @param array $options Extra options to pass to the "Provider" class
     *
     * @return RedirectResponse
     */
    public function redirect(array $scopes = [], array $options = [])
    {
        if (!empty($scopes)) {
            $options['scope'] = $scopes;
        }

        $url = $this->provider->getAuthorizationUrl($options);

        $this->getSession()->set(
            self::OAUTH2_SESSION_STATE_KEY,
            $this->provider->getState()
        );

        return new RedirectResponse($url);
    }

    /**
     * Call this after the user is redirected back to get the access token.
     *
     * @return AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface
     *
     * @throws InvalidStateException
     * @throws MissingAuthorizationCodeException
     * @throws IdentityProviderException         If token cannot be fetched
     */
    public function getAccessToken()
    {
        $expectedState = $this->getSession()->get(self::OAUTH2_SESSION_STATE_KEY);
        $server = $this->getSession()->get(self::SESSION_SERVER_KEY);
        $actualState = $this->getCurrentRequest()->query->get('state');
        if (!$actualState || ($actualState !== $expectedState)) {
            throw new InvalidStateException('Invalid state');
        }

        $code = $this->getCurrentRequest()->get('code');

        if (!$code) {
            throw new MissingAuthorizationCodeException('No "code" parameter was found (usually this is a query parameter)!');
        }

        return $this->provider->getAccessTokenWithServer($server, 'authorization_code', [
            'code' => $code
        ]);
    }

    /**
     * Returns the "User" information (called a resource owner).
     *
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUserFromToken(AccessToken $accessToken)
    {
        return $this->provider->getResourceOwner($accessToken);
    }

    /**
     * Shortcut to fetch the access token and user all at once.
     *
     * Only use this if you don't need the access token, but only
     * need the user.
     *
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUser()
    {
        /** @var AccessToken $token */
        $token = $this->getAccessToken();

        return $this->fetchUserFromToken($token);
    }

    /**
     * Returns the underlying OAuth2 provider.
     *
     * @return CastorUserProvider
     */
    public function getOAuth2Provider()
    {
        return $this->provider;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getCurrentRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            throw new \LogicException('There is no "current request", and it is needed to perform this action');
        }

        return $request;
    }

    /**
     * @return SessionInterface
     */
    private function getSession()
    {
        return $this->getCurrentRequest()->getSession();
    }
}