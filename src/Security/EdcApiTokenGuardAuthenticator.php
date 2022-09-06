<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Person;
use App\Model\Castor\ApiClient;
use App\Security\Providers\Castor\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function assert;
use function filter_var;
use function var_export;
use const FILTER_FLAG_HOSTNAME;

class EdcApiTokenGuardAuthenticator extends AbstractGuardAuthenticator
{
    private const HEADER_X_AUTH_TOKEN = 'X-AUTH-TOKEN';
    private const HEADER_X_AUTH_SERVER = 'X-AUTH-SERVER';

    protected ApiClient $apiClient;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(ApiClient $apiClient, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->apiClient = $apiClient;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::HEADER_X_AUTH_TOKEN) &&
            $request->headers->has(self::HEADER_X_AUTH_SERVER);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), 401);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('The X-AUTH-TOKEN and X-AUTH-SERVER headers are required.', 401);
    }

    /** @return array{api_token: string|null, edc_server: string|null} */
    public function getCredentials(Request $request)
    {
        return [
            'api_token' => $request->headers->get(self::HEADER_X_AUTH_TOKEN),
            'edc_server' => $request->headers->get(self::HEADER_X_AUTH_SERVER),
        ];
    }

    /**
     * @param array{api_token: string|null, edc_server: string|null} $credentials
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->apiClient->setServer($credentials['edc_server']);
        $this->apiClient->setToken($credentials['api_token']);

        try {
            $castorUser = $this->apiClient->getUser();
        } catch (\Throwable $t) {
            $this->logger->warning($t->getMessage());
            $this->logger->warning($t->getTraceAsString());
            throw new CustomUserMessageAuthenticationException('Failed to validate access token.');
        }
        $castorUser->setToken($credentials['api_token']);
        $castorUser->setServer($credentials['edc_server']);

        $dbUser = $this->entityManager->getRepository(CastorUser::class)->findOneBy(['id' => $castorUser->getId()]);
        assert($dbUser instanceof CastorUser || $dbUser === null);

        if ($dbUser === null) {
            // Return error response to enforce user to login first.
            throw new CustomUserMessageAuthenticationException(
                'You have to have a completed user profile on the FAIR Data Point before you can use the ' .
                'API. Please login manually at least once.'
            );
        } else {
            // Castor User Found, update user
            $dbUser->setNameFirst($castorUser->getNameFirst());
            $dbUser->setNameMiddle($castorUser->getNameMiddle());
            $dbUser->setNameLast($castorUser->getNameLast());

            $dbUser->setToken($castorUser->getToken());
            $dbUser->setServer($castorUser->getServer());

            $user = $dbUser->getUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createNewUser(CastorUser $castorUser): User
    {
        $person = new Person(
            $castorUser->getNameFirst(),
            $castorUser->getNameMiddle(),
            $castorUser->getNameLast(),
            $castorUser->getEmailAddress(),
            null,
            null,
            NameOrigin::castor()
        );

        return new User($person);
    }

    /** @return true */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
