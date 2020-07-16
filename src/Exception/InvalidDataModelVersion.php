<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidDataModelVersion extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Invalid data model version.'];
    }
}
