<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\PermissionType;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class Permission
{
    protected User $user;

    /** @ORM\Column(type="PermissionType") */
    protected PermissionType $type;

    public function __construct(User $user, PermissionType $type)
    {
        $this->user = $user;
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
}
