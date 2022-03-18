<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class MappingAlreadyExists extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'There is already a mapping for this node. Remove this mapping before adding a new one.'];
    }
}
