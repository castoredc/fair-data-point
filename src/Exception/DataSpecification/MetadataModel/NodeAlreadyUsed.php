<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\MetadataModel;

use Exception;
use function sprintf;

class NodeAlreadyUsed extends Exception
{
    public function __construct(private ?string $fieldName = null)
    {
        parent::__construct();
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        if ($this->fieldName !== null) {
            return ['error' => sprintf('This node is already used by field \'%s\'.', $this->fieldName)];
        }

        return ['error' => 'This node is already used'];
    }
}
