<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\PermissionType;
use Doctrine\Common\Collections\Collection;

interface PermissionsEnabledEntity
{
    public function getId(): string;

    public function addPermissionForUser(User $user, PermissionType $type): Permission;

    public function removePermissionForUser(User $user): void;

    public function getPermissionsForUser(User $user): ?Permission;

    /** @return Collection<Permission> */
    public function getPermissions(): Collection;

    /** @return PermissionType[] */
    public function supportsPermissions(): array;
}
