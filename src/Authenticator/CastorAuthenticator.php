<?php
declare(strict_types=1);

namespace App\Authenticator;
use App\Model\UnverifiedSSLOAuthClient;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Temp solution for Authentication of some endpoints. Should be replaced with proper Authentication using User
 * objects etc
 *
 * @package App\Authenticator
 */
class CastorAuthenticator
{

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $redirectURL;

    /**
     * @var string
     */
    private $authURL;

    /**
     * @var GenericProvider
     */
    private $oAuthProvider;


    /**
     * CastorAuthenticator constructor.
     * @param string $clientId
     * @param string $secret
     * @param string $redirectURL
     * @param string $authURL
     * @param bool $verifySSL
     */
    public function __construct(
        string $clientId,
        string $secret,
        string $redirectURL,
        string $authURL,
        bool $verifySSL = true)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->redirectURL = $redirectURL;
        $this->authURL = $authURL;
        
        $providerConfig = [
            'clientId'                => $this->clientId,
            'clientSecret'            => $this->secret,
            'redirectUri'             => $this->redirectURL,
            'urlAuthorize'            => $this->authURL . '/oauth/authorize',
            'urlAccessToken'          => $this->authURL . '/oauth/token',
            'urlResourceOwnerDetails' => $this->authURL . '/oauth/resource'
        ];
        if (!$verifySSL) {
            $providerConfig['verify'] = false;
            $this->oAuthProvider = new UnverifiedSSLOAuthClient($providerConfig);
        } else {
            $this->oAuthProvider = new GenericProvider($providerConfig);
        }
    }

    public function hasAccess($token): bool
    {
        return !empty($token) && $this->isTokenStillValid($token);
    }

    public function getAccessTokenByCode(string $code): string
    {
        return $this->oAuthProvider->getAccessToken('authorization_code', ['code' => $code])->getToken();
    }

    public function getAuthorizationUrl(): string
    {
        return $this->oAuthProvider->getAuthorizationUrl();
    }


    private function isTokenStillValid($token)
    {
        # Make sure we have access to studies
        $request = $this->oAuthProvider->getAuthenticatedRequest(
            'GET',
            $_SERVER['CASTOR_API_URL'] . '/api/study',
            new AccessToken(['access_token' => $token]),
            ['headers' => ['Accept' => 'application/json']]
        );
        try {
            $this->oAuthProvider->getHttpClient()->send($request);
        } catch (\Throwable $x) {
            return false;
        }
        return true;
    }

}