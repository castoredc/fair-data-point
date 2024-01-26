<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationPrefixApiResource;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;

class MetadataModelPrefixApiResource extends DataSpecificationPrefixApiResource
{
    public function __construct(NamespacePrefix $prefix)
    {
        parent::__construct($prefix);
    }
}
