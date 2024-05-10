<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Common\OptionGroupsApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class MetadataModelVersionExportApiResource implements ApiResource
{
    public function __construct(private MetadataModelVersion $metadataModelVersion)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'model' => (new MetadataModelApiResource($this->metadataModelVersion->getMetadataModel(), false))->toArray(),
            'version' => (new MetadataModelVersionApiResource($this->metadataModelVersion))->toArray(),
            'nodes' => (new NodesApiResource($this->metadataModelVersion))->toArray(),
            'modules' => (new MetadataModelModulesApiResource($this->metadataModelVersion, false))->toArray(),
            'prefixes' => (new MetadataModelPrefixesApiResource($this->metadataModelVersion))->toArray(),
            'predicates' => (new PredicatesApiResource($this->metadataModelVersion))->toArray(),
            'optionGroups' => (new OptionGroupsApiResource($this->metadataModelVersion))->toArray(),
        ];
    }
}
