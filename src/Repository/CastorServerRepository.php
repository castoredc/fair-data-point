<?php
declare(strict_types=1);

namespace App\Repository;

use App\Security\CastorServer;
use Doctrine\ORM\EntityRepository;

/**
 * @method CastorServer|null findOneByName(string $name)
 * @method CastorServer|null findOneById(string $id)
 * @method CastorServer[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method CastorServer|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method CastorServer[] findAll()
 */
class CastorServerRepository extends EntityRepository
{
    public function findServerByUrl(string $url): ?CastorServer
    {
        return $this->findOneBy(['url' => $url]);
    }
}
