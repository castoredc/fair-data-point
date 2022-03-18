<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidEntityType extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'This type of entity is not supported.'];
    }
}
