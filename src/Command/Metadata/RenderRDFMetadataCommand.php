<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\FAIRData\MetadataEnrichedEntity;

class RenderRDFMetadataCommand
{
    public function __construct(private MetadataEnrichedEntity $entity)
    {
    }

    public function getEntity(): MetadataEnrichedEntity
    {
        return $this->entity;
    }
}
