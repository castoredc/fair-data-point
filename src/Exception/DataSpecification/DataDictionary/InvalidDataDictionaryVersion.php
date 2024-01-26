<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\DataDictionary;

use Exception;

class InvalidDataDictionaryVersion extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'Invalid data model version.'];
    }
}
