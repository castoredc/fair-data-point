<?php
declare(strict_types=1);

namespace App\Command\Security;

use App\Entity\Enum\PermissionType;
use App\Security\PermissionsEnabledEntity;

class AddPermissionToEntityCommand
{
    public function __construct(private PermissionsEnabledEntity $entity, private string $email, private PermissionType $type)
    {
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
