<?php
declare(strict_types=1);

namespace App\Security\Providers\Castor;

use App\Exception\CouldNotDecrypt;
use App\Model\Castor\ApiClient;
use App\Security\CastorServer;
use App\Security\Providers\UserProvider;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function assert;
use function trim;

class CastorUserProvider extends UserProvider implements UserProviderInterface
{
    use BearerAuthorizationTrait;

    protected string $server;

    private ?ApiClient $apiClient = null;
    private EncryptionService $encryptionService;

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(
        EntityManagerInterface $em,
        ApiClient $apiClient,
        EncryptionService $encryptionService,
        array $options = [],
        array $collaborators = []
    ) {
        parent::__construct($em, $options, $collaborators);

        $this->em = $em;
        $this->apiClient = $apiClient;
        $this->encryptionService = $encryptionService;
    }

    public function getBaseAuthorizationUrl(): string
    {
        return '/oauth/authorize';
    }

    /**
     * @param array<mixed> $options
     *
     * @throws CouldNotDecrypt
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        if (! isset($options['server']) || ! isset($options['server_id'])) {
            return '';
        }

        $this->overrideClientCredentialsFromDatabase($options['server_id']);

        $base = $options['server'] . $this->getBaseAuthorizationUrl();
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        return $this->appendQuery($base, $query);
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param array<mixed> $options
     *
     * @throws IdentityProviderException
     * @throws CouldNotDecrypt
     */
    public function getAccessTokenWithServer(
        string $server,
        int $serverId,
        mixed $grant,
        array $options = []
    ): AccessTokenInterface {
        $this->server = $server;

        $this->overrideClientCredentialsFromDatabase($serverId);

        return parent::getAccessToken($grant, $options);
    }

    /** @param array<mixed> $params */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->server . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->server . '/api/user';
    }

    /** @return array<string> */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /** @inheritDoc */
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

    private function overrideClientCredentialsFromDatabase(int $serverId): void
    {
        $castorServer = $this->em->getRepository(CastorServer::class)->find($serverId);
        assert($castorServer instanceof CastorServer);

        try {
            $clientId = trim($castorServer->getDecryptedClientId($this->encryptionService)->exposeAsString());
            $clientSecret = trim($castorServer->getDecryptedClientSecret($this->encryptionService)->exposeAsString());
        } catch (CouldNotDecrypt $e) {
            // Don't do anything, fallback to the default credentials supplied in the env file.
            return;
        }

        if ($clientId === '' || $clientSecret === '') {
            return;
        }

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // TODO: Implement loadUserByIdentifier() method.
    }
}
