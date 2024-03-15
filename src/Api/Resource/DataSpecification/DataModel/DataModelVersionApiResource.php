<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationVersionApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class DataModelVersionApiResource extends DataSpecificationVersionApiResource
{
    public function __construct(DataModelVersion $dataModelVersion)
    {
        parent::__construct($dataModelVersion);
    }
}
