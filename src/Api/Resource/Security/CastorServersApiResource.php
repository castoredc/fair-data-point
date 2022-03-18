<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;
use App\Security\CastorServer;

class CastorServersApiResource implements ApiResource
{
    /** @var CastorServer[] */
    private array $servers;

    /** @param CastorServer[] $servers */
    public function __construct(array $servers)
    {
        $this->servers = $servers;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->servers as $server) {
            $data[] = (new CastorServerApiResource($server))->toArray();
        }

        return $data;
    }
}
