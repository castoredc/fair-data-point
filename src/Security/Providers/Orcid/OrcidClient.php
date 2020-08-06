<?php
declare(strict_types=1);

namespace App\Security\Providers\Orcid;

use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use LogicException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use function count;

class OrcidClient extends OAuth2Client
{
    public const OAUTH2_SESSION_STATE_KEY = 'knpu.oauth2_client_state';

    /** @var OrcidUserProvider */
    private $provider;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(OrcidUserProvider $provider, RequestStack $requestStack)
    {
        parent::__construct($provider, $requestStack);

        $this->provider = $provider;
        $this->requestStack = $requestStack;
    }

    /**
     * Creates a RedirectResponse that will send the user to the
     * OAuth2 server (e.g. send them to Facebook).
     *
     * @param array<mixed> $scopes  The scopes you want (leave empty to use default)
     * @param array<mixed> $options Extra options to pass to the "Provider" class
     */
    public function redirect(array $scopes = [], array $options = []): RedirectResponse
    {
        if (count($scopes) !== 0) {
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
     * Returns the underlying OAuth2 provider.
     */
    public function getOAuth2Provider(): OrcidUserProvider
    {
        return $this->provider;
    }

    private function getCurrentRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new LogicException('There is no "current request", and it is needed to perform this action');
        }

        return $request;
    }

    private function getSession(): SessionInterface
    {
        return $this->getCurrentRequest()->getSession();
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(array $options = [])
    {
        $expectedState = $this->getSession()->get(self::OAUTH2_SESSION_STATE_KEY);

        $actualState = $this->getCurrentRequest()->query->get('state');
        if ($actualState === null || ($actualState !== $expectedState)) {
            throw new InvalidStateException('Invalid state');
        }

        $code = $this->getCurrentRequest()->get('code');

        if ($code === null) {
            throw new MissingAuthorizationCodeException('No "code" parameter was found (usually this is a query parameter)!');
        }

        return $this->provider->getAccessToken('authorization_code', ['code' => $code]);
    }
}
