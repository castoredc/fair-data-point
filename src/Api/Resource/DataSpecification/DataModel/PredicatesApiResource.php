<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\PredicatesApiResource as CommonPredicatesApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class PredicatesApiResource extends CommonPredicatesApiResource
{
    public function __construct(DataModelVersion $dataModel)
    {
        parent::__construct($dataModel);
    }
}
