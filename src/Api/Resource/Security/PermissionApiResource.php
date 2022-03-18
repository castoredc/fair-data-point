<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\ApiResource;
use App\Security\Permission;

class PermissionApiResource implements ApiResource
{
    private Permission $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->permission->getUser()->getId(),
                'name' => $this->permission->getUser()->getPerson() !== null ? $this->permission->getUser()->getPerson()->getName() : '',
            ],
            'type' => $this->permission->getType()->toString(),
        ];
    }
}
