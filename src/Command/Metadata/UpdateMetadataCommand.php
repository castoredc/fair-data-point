<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Metadata\Metadata;

class UpdateMetadataCommand
{
    /** @param array<string, mixed> $values */
    public function __construct(
        private Metadata $metadata,
        private array $values,
    ) {
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /** @return array<string, mixed> */
    public function getValues(): array
    {
        return $this->values;
    }
}
