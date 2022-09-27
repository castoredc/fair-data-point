<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class PermissionTypeNotSupported extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'This permission type is not supported for this entity.'];
    }
}
