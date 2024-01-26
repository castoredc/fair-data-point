<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;

class MetadataModelApiResource extends DataSpecificationApiResource
{
    public function __construct(MetadataModel $metadataModel, bool $includeVersions = true)
    {
        parent::__construct($metadataModel, $includeVersions);
    }
}
