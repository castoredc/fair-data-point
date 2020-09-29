<?php
declare(strict_types=1);

namespace App\Security\Providers\Castor;

use App\Model\Castor\ApiClient;
use App\Security\Providers\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CastorUserProvider extends UserProvider implements UserProviderInterface
{
    use BearerAuthorizationTrait;

    protected string $server;

    private ?ApiClient $apiClient = null;

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(EntityManagerInterface $em, ApiClient $apiClient, array $options = [], array $collaborators = [])
    {
        parent::__construct($em, $options, $collaborators);

        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    public function getBaseAuthorizationUrl(): string
    {
        return '/oauth/authorize';
    }

    /**
     * @param array<mixed> $options
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        if (! isset($options['server'])) {
            return '';
        }

        $base = $options['server'] . $this->getBaseAuthorizationUrl();
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        return $this->appendQuery($base, $query);
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed        $grant
     * @param  array<mixed> $options
     *
     * @throws IdentityProviderException
     */
    public function getAccessTokenWithServer(string $server, $grant, array $options = []): AccessTokenInterface
    {
        $this->server = $server;

        return parent::getAccessToken($grant, $options);
    }

    /**
     * @param array<mixed> $params
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->server . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->server . '/api/user';
    }

    /**
     * @return array<string>
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * @param array<mixed>|string $data
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        // TODO: Implement checkResponse() method.
    }

    /**
     * @param array<mixed> $response
     *
     * @throws Exception
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        $this->apiClient->setServer($this->server);
        $this->apiClient->setToken($token->getToken());

        $user = $this->apiClient->getUser();
        $user->setToken($token->getToken());
        $user->setServer($this->server);

        return $user;
    }
}
