<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\PermissionType;

interface PermissionsEnabledEntity
{
    public function addPermissionForUser(User $user, PermissionType $type): Permission;

    public function removePermissionForUser(User $user): void;

    public function getPermissionsForUser(User $user): ?Permission;
}
