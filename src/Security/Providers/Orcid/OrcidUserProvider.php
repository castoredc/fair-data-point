<?php
declare(strict_types=1);

namespace App\Security\Providers\Orcid;

use App\Security\Providers\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OrcidUserProvider extends UserProvider implements UserProviderInterface
{
    use BearerAuthorizationTrait;

    public const BASE_URL = 'https://orcid.org';

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(EntityManagerInterface $em, array $options = [], array $collaborators = [])
    {
        parent::__construct($em, $options, $collaborators);

        $this->em = $em;
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
        $base = self::BASE_URL . $this->getBaseAuthorizationUrl();
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        return $this->appendQuery($base, $query);
    }

    /**
     * @param array<mixed> $params
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::BASE_URL . '/oauth/token';
    }

    /**
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return '';
    }

    /**
     * @return array<string>
     */
    protected function getDefaultScopes(): array
    {
        return ['/authenticate'];
    }

    /**
     * @param array<mixed>|string $data
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        // TODO: Implement checkResponse() method.
    }

    public function getResourceOwner(AccessToken $token): ResourceOwnerInterface
    {
        return $this->createResourceOwner($token->getValues(), $token);
    }

    /**
     * @param array<mixed> $response
     *
     * @throws Exception
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new OrcidUser($response['orcid'], $response['name'], $token->getToken());
    }
}
