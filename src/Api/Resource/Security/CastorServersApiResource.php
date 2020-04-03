<?php

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\CastorServer;
use App\Entity\FAIRData\Department;

class CastorServersApiResource implements ApiResource
{
    /** @var CastorServer[] */
    private $servers;

    /**
     * @param CastorServer[] $servers
     */
    public function __construct(array $servers)
    {
        $this->servers = $servers;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->servers as $server) {
            $data[] = (new CastorServerApiResource($server))->toArray();
        }

        return $data;
    }
}