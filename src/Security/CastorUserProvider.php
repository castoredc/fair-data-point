<?php
declare(strict_types=1);

namespace App\Security;

use App\Model\Castor\ApiClient;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CastorUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(ManagerRegistry $doctrine, ApiClient $apiClient)
    {
        $this->doctrine = $doctrine;
        $this->apiClient = $apiClient;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @throws UsernameNotFoundException if the user is not found
     * @throws Exception
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $this->apiClient->setToken($response->getAccessToken());

        $castorUser = $this->apiClient->getUser();

        $userRepository = $this->doctrine->getRepository(CastorUser::class);
        $dbUser = $userRepository->find($castorUser->getId());

        if ($dbUser !== null) {
            $dbUser->setToken($response->getAccessToken());

            return $dbUser;
        }

        $user = CastorUser::fromData($castorUser, $response->getAccessToken());

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     * @inheritDoc
     */
    public function loadUserByUsername($username): UserInterface
    {
        throw new UsernameNotFoundException();
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     *
     * @inheritDoc
     */
    public function supportsClass($class): bool
    {
        return $class === CastorUser::class;
    }
}
