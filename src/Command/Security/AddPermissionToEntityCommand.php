<?php
declare(strict_types=1);

namespace App\Command\Security;

use App\Entity\Enum\PermissionType;
use App\Security\PermissionsEnabledEntity;

class AddPermissionToEntityCommand
{
    private PermissionsEnabledEntity $entity;

    private string $email;

    private PermissionType $type;

    public function __construct(PermissionsEnabledEntity $entity, string $email, PermissionType $type)
    {
        $this->entity = $entity;
        $this->email = $email;
        $this->type = $type;
    }

    public function getEntity(): PermissionsEnabledEntity
    {
        return $this->entity;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): PermissionType
    {
        return $this->type;
    }
}
