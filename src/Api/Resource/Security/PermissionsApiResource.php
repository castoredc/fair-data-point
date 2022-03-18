<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;

class PermissionsApiResource implements ApiResource
{
    /** @var string[] */
    protected array $permissions;

    /** @param string[] $permissions */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /** @return array<string, string[]> */
    public function toArray(): array
    {
        return [
            'permissions' => $this->permissions,
        ];
    }
}
