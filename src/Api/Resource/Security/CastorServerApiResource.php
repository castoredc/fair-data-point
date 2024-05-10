<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\RoleBasedApiResource;
use App\Security\CastorServer;
use App\Service\EncryptionService;

final class CastorServerApiResource extends RoleBasedApiResource
{
    public function __construct(private CastorServer $server, bool $isAdmin, private EncryptionService $encryptionService)
    {
        $this->isAdmin = $isAdmin;
    }

    /** @return array{id: ?int, url: string, name: string, flag: string, default: bool, clientId?: string, clientSecret?: string} */
    public function toArray(): array
    {
        $serverSerialized = [
            'id' => $this->server->getId(),
            'url' => $this->server->getUrl()->getValue(),
            'name' => $this->server->getName(),
            'flag' => $this->server->getFlag(),
            'default' => $this->server->isDefault(),
        ];

        if ($this->isAdmin) {
            $serverSerialized['clientId'] = $this->server->getDecryptedClientId($this->encryptionService)->exposeAsString();
            $serverSerialized['clientSecret'] = $this->server->getDecryptedClientSecret($this->encryptionService)->exposeAsString();
        }

        return $serverSerialized;
    }
}
