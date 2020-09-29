<?php
declare(strict_types=1);

namespace App\Repository;

use App\Security\CastorServer;
use Doctrine\ORM\EntityRepository;
use function assert;

class CastorServerRepository extends EntityRepository
{
    public function findServerByUrl(string $url): ?CastorServer
    {
        $server = $this->findOneBy(['url' => $url]);
        assert($server instanceof CastorServer || $server === null);

        return $server;
    }
}
