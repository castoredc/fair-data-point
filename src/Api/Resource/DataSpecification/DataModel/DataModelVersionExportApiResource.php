<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class DataModelVersionExportApiResource implements ApiResource
{
    public function __construct(private DataModelVersion $dataModelVersion)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'model' => (new DataModelApiResource($this->dataModelVersion->getDataModel(), false))->toArray(),
            'version' => (new DataModelVersionApiResource($this->dataModelVersion))->toArray(),
            'nodes' => (new NodesApiResource($this->dataModelVersion))->toArray(),
            'modules' => (new DataModelModulesApiResource($this->dataModelVersion, false))->toArray(),
            'prefixes' => (new DataModelPrefixesApiResource($this->dataModelVersion))->toArray(),
            'predicates' => (new PredicatesApiResource($this->dataModelVersion))->toArray(),
        ];
    }
}
