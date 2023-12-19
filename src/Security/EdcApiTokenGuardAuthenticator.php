<?php
declare(strict_types=1);

namespace App\Security;

use App\Model\Castor\ApiClient;
use App\Security\Providers\Castor\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Throwable;
use function assert;

class EdcApiTokenGuardAuthenticator extends AbstractGuardAuthenticator
{
    private const HEADER_X_AUTH_TOKEN = 'X-AUTH-TOKEN';
    private const HEADER_X_AUTH_SERVER = 'X-AUTH-SERVER';
    private const CREDENTIALS_API_TOKEN = 'api_token';
    private const CREDENTIALS_EDC_SERVER = 'edc_server';

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
        return new Response($exception->getMessage(), \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
    }

    /** @inheritDoc */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new Response('The X-AUTH-TOKEN and X-AUTH-SERVER headers are required.', \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
    }

    /** @return array{api_token: string|null, edc_server: string|null} */
    public function getCredentials(Request $request): array
    {
        return [
            self::CREDENTIALS_API_TOKEN => $request->headers->get(self::HEADER_X_AUTH_TOKEN),
            self::CREDENTIALS_EDC_SERVER => $request->headers->get(self::HEADER_X_AUTH_SERVER),
        ];
    }

    /** @inheritDoc */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        // Validate the supplied edc server
        $edcServer = $this->entityManager->getRepository(CastorServer::class)->findOneBy(['url' => $credentials[self::CREDENTIALS_EDC_SERVER]]);
        if ($edcServer === null) {
            throw new CustomUserMessageAuthenticationException('Invalid X-AUTH-SERVER provided.');
        }

        $this->apiClient->setServer($credentials[self::CREDENTIALS_EDC_SERVER]);
        $this->apiClient->setToken($credentials[self::CREDENTIALS_API_TOKEN]);

        try {
            $castorUser = $this->apiClient->getUser();
        } catch (Throwable $t) {
            $this->logger->warning($t->getMessage());
            $this->logger->warning($t->getTraceAsString());

            throw new CustomUserMessageAuthenticationException('Failed to validate access token.');
        }

        $castorUser->setToken($credentials[self::CREDENTIALS_API_TOKEN]);
        $castorUser->setServer($credentials[self::CREDENTIALS_EDC_SERVER]);

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

    /** @inheritDoc */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /** @inheritDoc */
    public function supportsRememberMe()
    {
        return false;
    }
}
