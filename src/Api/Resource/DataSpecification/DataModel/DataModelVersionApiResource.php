<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationVersionApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class DataModelVersionApiResource extends DataSpecificationVersionApiResource
{
    public function __construct(private DataModelVersion $dataModelVersion)
    {
        parent::__construct($dataModelVersion);
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'dataModel' => $this->dataModelVersion->getDataModel()->getId(),
            ]
        );
    }
}
