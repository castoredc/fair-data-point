<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;
use App\Security\CastorServer;

class CastorServerApiResource implements ApiResource
{
    private CastorServer $server;

    public function __construct(CastorServer $server)
    {
        $this->server = $server;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->server->getId(),
            'url' => $this->server->getUrl()->getValue(),
            'name' => $this->server->getName(),
            'flag' => $this->server->getFlag(),
            'default' => $this->server->isDefault(),
        ];
    }
}
