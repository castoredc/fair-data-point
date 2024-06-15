<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;

class MetadataModelsApiResource implements ApiResource
{
    /** @param MetadataModel[] $metadataModels */
    public function __construct(private array $metadataModels)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->metadataModels as $metadataModel) {
            $data[] = (new MetadataModelApiResource($metadataModel))->toArray();
        }

        return $data;
    }
}
