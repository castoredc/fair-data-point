<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class MetadataModelPrefixesApiResource implements ApiResource
{
    public function __construct(private MetadataModelVersion $metadataModel)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->metadataModel->getPrefixes() as $prefix) {
            $data[] = (new MetadataModelPrefixApiResource($prefix))->toArray();
        }

        return $data;
    }
}
