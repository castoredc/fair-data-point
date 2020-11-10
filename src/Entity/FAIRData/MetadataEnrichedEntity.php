<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Metadata\Metadata;

interface MetadataEnrichedEntity
{
    public function hasMetadata(): bool;

    public function getLatestMetadata(): ?Metadata;
}
