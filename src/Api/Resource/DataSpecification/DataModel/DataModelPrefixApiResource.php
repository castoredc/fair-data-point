<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationPrefixApiResource;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;

class DataModelPrefixApiResource extends DataSpecificationPrefixApiResource
{
    public function __construct(NamespacePrefix $prefix)
    {
        parent::__construct($prefix);
    }
}
