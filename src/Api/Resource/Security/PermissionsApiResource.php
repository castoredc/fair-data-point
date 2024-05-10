<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;

class PermissionsApiResource implements ApiResource
{
    /** @param string[] $permissions */
    public function __construct(protected array $permissions)
    {
    }

    /** @return array<string, string[]> */
    public function toArray(): array
    {
        return [
            'permissions' => $this->permissions,
        ];
    }
}
