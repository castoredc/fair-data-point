<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\MetadataModel;

use Exception;

class NodeHasValues extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'This node has values collected for it, and cannot be removed'];
    }
}
