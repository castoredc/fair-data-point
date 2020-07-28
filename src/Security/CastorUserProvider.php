<?php
declare(strict_types=1);

namespace App\Security;

use App\Exception\UserNotACastorUser;
use App\Exception\UserNotFound;
use App\Model\Castor\ApiClient;
use App\Repository\CastorUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
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

    /** @var EntityManagerInterface|null */
    private $em;

    /** @var ApiClient|null */
    private $apiClient;

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(EntityManagerInterface $em, ApiClient $apiClient, array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
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
        $this->apiClient->setServer($this->server);
        $this->apiClient->setToken($token->getToken());

        return CastorUser::fromData($this->apiClient->getUser(), $token->getToken(), $this->server);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return new User(null, null);
    }

    /**
     * @throws UserNotFound
     * @throws UserNotACastorUser
     *
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof CastorUser) {
            throw new UserNotACastorUser();
        }

        /** @var CastorUserRepository $userRepository */
        $userRepository = $this->em->getRepository(CastorUser::class);
        $dbUser = $userRepository->findUserByEmail($user->getEmailAddress());
        $dbUser->setToken($user->getToken());
        $dbUser->setServer($user->getServer());
        $dbUser->setStudies($user->getStudies());

        return $dbUser;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return true;
    }
}
