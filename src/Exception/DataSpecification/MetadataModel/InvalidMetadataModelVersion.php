<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\MetadataModel;

use Exception;

class InvalidMetadataModelVersion extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'Invalid metadata model version.'];
    }
}
