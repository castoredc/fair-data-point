<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\RoleBasedApiResource;
use App\Security\CastorServer;
use App\Service\EncryptionService;

final class CastorServerApiResource extends RoleBasedApiResource
{
    private CastorServer $server;
    private EncryptionService $encryptionService;

    public function __construct(CastorServer $server, bool $isAdmin, EncryptionService $encryptionService)
    {
        $this->server = $server;
        $this->isAdmin = $isAdmin;
        $this->encryptionService = $encryptionService;
    }

    /** @return array{id: ?int, url: string, name: string, flag: string, default: bool, client_id?: string, client_secret?: string} */
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
