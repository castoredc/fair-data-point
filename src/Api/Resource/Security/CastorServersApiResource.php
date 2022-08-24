<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\RoleBasedApiResource;
use App\Security\CastorServer;
use App\Service\EncryptionService;

final class CastorServersApiResource extends RoleBasedApiResource
{
    /** @var CastorServer[] */
    private array $servers;
    private EncryptionService $encryptionService;

    /** @param CastorServer[] $servers */
    public function __construct(array $servers, bool $isAdmin, EncryptionService $encryptionService)
    {
        $this->servers = $servers;
        $this->isAdmin = $isAdmin;
        $this->encryptionService = $encryptionService;
    }

    /** @return array<array{id: ?int, url: string, name: string, flag: string, default: bool, client_id?: string, client_secret?: string}> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->servers as $server) {
            $data[] = (new CastorServerApiResource($server, $this->isAdmin, $this->encryptionService))->toArray();
        }

        return $data;
    }
}
