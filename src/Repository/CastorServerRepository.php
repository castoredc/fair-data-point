<?php
declare(strict_types=1);

namespace App\Repository;

use App\Security\CastorServer;
use Doctrine\ORM\EntityRepository;

class CastorServerRepository extends EntityRepository
{
    public function findServerByUrl(string $url): ?CastorServer
    {
        /** @var CastorServer|null $server */
        $server = $this->findOneBy(['url' => $url]);

        return $server;
    }
}
