<?php
declare(strict_types=1);

namespace App\Security\Providers;

use App\Exception\UserNotFound;
use App\Security\User as AppUser;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function assert;

abstract class UserProvider extends AbstractProvider implements UserProviderInterface
{
    use BearerAuthorizationTrait;

    protected string $server;

    protected ?EntityManagerInterface $em = null;

    /**
     * @param array<mixed> $options
     * @param array<mixed> $collaborators
     */
    public function __construct(EntityManagerInterface $em, array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $this->em = $em;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return new InMemoryUser(null, null);
    }

    /**
     * @throws UserNotFound
     *
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        assert($user instanceof AppUser);

        $userRepository = $this->em->getRepository(AppUser::class);

        $dbUser = $userRepository->find($user->getId());
        assert($dbUser instanceof AppUser);

        if ($user->hasCastorUser()) {
            $castorUser = $user->getCastorUser();
            $dbCastorUser = $dbUser->getCastorUser();

            $dbCastorUser->setServer($castorUser->getServer());

            if ($castorUser->isAuthenticated()) {
                $dbCastorUser->setToken($castorUser->getToken());
                $dbCastorUser->setStudies($castorUser->getStudies());
            }

            $dbUser->setCastorUser($dbCastorUser);
        }

        return $dbUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === AppUser::class;
    }
}
