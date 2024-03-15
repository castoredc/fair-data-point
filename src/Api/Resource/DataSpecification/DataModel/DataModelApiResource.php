<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationApiResource;
use App\Entity\DataSpecification\DataModel\DataModel;

class DataModelApiResource extends DataSpecificationApiResource
{
    public function __construct(DataModel $dataModel, bool $includeVersions = true)
    {
        parent::__construct($dataModel, $includeVersions);
    }
}
