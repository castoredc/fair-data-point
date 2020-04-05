<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\CastorServer;
use App\Exception\UserNotFound;
use App\Security\CastorUser;
use Doctrine\ORM\EntityRepository;

class CastorUserRepository extends EntityRepository
{
    /**
     * @throws UserNotFound
     */
    public function findUserByEmail(string $email): CastorUser
    {
        /** @var CastorUser|null $user */
        $user = $this->findOneBy(['emailAddress' => $email]);

        if($user === null)
        {
            throw new UserNotFound();
        }

        return $user;
    }
}
