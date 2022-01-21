<?php
declare(strict_types=1);

namespace App\Security;

interface PermissionsEnabledEntity
{
    public function getPermissionsForUser(User $user): ?Permission;
}
