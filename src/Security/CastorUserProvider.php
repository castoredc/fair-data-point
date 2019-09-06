<?php
namespace App\Security;

use App\Model\Castor\ApiClient;
use Doctrine\Common\Persistence\ManagerRegistry;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by PhpStorm.
 * User: Martijn
 * Date: 21/06/2018
 * Time: 11:00
 */

class CastorUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface {

    protected $doctrine;

    public function __construct (ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface $response
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException if the user is not found
     * @throws \Exception
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $castorApi = new ApiClient();
        $castorApi->setToken($response->getAccessToken());

        $castorUser = $castorApi->getUser();

        $userRepository = $this->doctrine->getRepository(CastorUser::class);
        $dbUser = $userRepository->find($castorUser->getId());

        if ($dbUser) {
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
     */
    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }
}