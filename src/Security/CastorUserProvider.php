<?php
declare(strict_types=1);

namespace App\Security;

use App\Model\Castor\ApiClient;
use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CastorUserProvider extends AbstractProvider implements UserProviderInterface
{
    use BearerAuthorizationTrait;

    /** @var string */
    protected $server;

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        if (! isset($options['server'])) {
            return;
        }

        $this->server = $options['server'];
    }

    /**
     * @inheritDoc
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->server . '/oauth/authorize';
    }

    /**
     * @param array<mixed> $params
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->server . '/oauth/token';
    }

    /**
     * @inheritDoc
     */
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
        $apiClient = new ApiClient($this->server);
        $apiClient->setToken($token->getToken());

        return CastorUser::fromData($apiClient->getUser(), $token->getToken());
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return new User(null, null);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return true;
    }
}
