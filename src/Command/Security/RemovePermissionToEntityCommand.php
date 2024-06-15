<?php
declare(strict_types=1);

namespace App\Command\Security;

use App\Security\PermissionsEnabledEntity;
use App\Security\User;

class RemovePermissionToEntityCommand
{
    public function __construct(private PermissionsEnabledEntity $entity, private User $user)
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
}
