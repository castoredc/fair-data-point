<?php
declare(strict_types=1);

namespace App\Command\Security;

use App\Entity\Enum\PermissionType;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;

class EditPermissionToEntityCommand
{
    public function __construct(private PermissionsEnabledEntity $entity, private User $user, private PermissionType $type)
    {
    }

    public function getEntity(): PermissionsEnabledEntity
    {
        return $this->entity;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): PermissionType
    {
        return $this->type;
    }
}
