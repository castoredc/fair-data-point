<?php
declare(strict_types=1);

namespace App\Repository;

use App\Exception\UserNotFound;
use App\Security\Providers\Castor\CastorUser;
use Doctrine\ORM\EntityRepository;
use function assert;

class CastorUserRepository extends EntityRepository
{
    /**
     * @throws UserNotFound
     */
    public function findUserByEmail(string $email): CastorUser
    {
        $user = $this->findOneBy(['emailAddress' => $email]);
        assert($user instanceof CastorUser || $user === null);

        if ($user === null) {
            throw new UserNotFound();
        }

        return $user;
    }
}
