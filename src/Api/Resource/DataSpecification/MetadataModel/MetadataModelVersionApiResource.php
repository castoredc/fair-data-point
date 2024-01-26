<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationVersionApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class MetadataModelVersionApiResource extends DataSpecificationVersionApiResource
{
    public function __construct(MetadataModelVersion $metadataModelVersion)
    {
        parent::__construct($metadataModelVersion);
    }
}
