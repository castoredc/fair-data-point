<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\PermissionType;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class Permission
{
    /** @ORM\Column(type="PermissionType") */
    protected PermissionType $type;

    public function __construct(protected User $user, PermissionType $type)
    {
        $this->type = $type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): PermissionType
    {
        return $this->type;
    }

    public function setType(PermissionType $type): void
    {
        $this->type = $type;
    }

    abstract public function getEntity(): PermissionsEnabledEntity;

    public static function entitySupportsPermission(PermissionsEnabledEntity $entity, PermissionType $type): bool
    {
        foreach ($entity->supportsPermissions() as $permission) {
            if ($type->isEqualTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
