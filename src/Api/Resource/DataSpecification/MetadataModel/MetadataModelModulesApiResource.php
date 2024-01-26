<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use function assert;

class MetadataModelModulesApiResource implements ApiResource
{
    private MetadataModelVersion $metadataModel;

    private bool $groupTriples;

    public function __construct(MetadataModelVersion $metadataModel, bool $groupTriples = true)
    {
        $this->metadataModel = $metadataModel;
        $this->groupTriples = $groupTriples;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->metadataModel->getGroups() as $group) {
            assert($group instanceof MetadataModelGroup);
            $data[] = (new MetadataModelModuleApiResource($group, $this->groupTriples))->toArray();
        }

        return $data;
    }
}
