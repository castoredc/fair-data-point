<?php
declare(strict_types=1);

namespace App\Command\Security;

use App\Security\PermissionsEnabledEntity;
use App\Security\User;

class RemovePermissionToEntityCommand
{
    private PermissionsEnabledEntity $entity;

    private User $user;

    public function __construct(PermissionsEnabledEntity $entity, User $user)
    {
        $this->entity = $entity;
        $this->user = $user;
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
