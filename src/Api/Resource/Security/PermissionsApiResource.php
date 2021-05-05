<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;

class PermissionsApiResource implements ApiResource
{
    /** @var array<string, bool> */
    protected array $permissions;

    /**
     * @param array<string, bool> $permissions
     */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function toArray(): array
    {
        return [
            'permissions' => $this->permissions
        ];
    }
}
