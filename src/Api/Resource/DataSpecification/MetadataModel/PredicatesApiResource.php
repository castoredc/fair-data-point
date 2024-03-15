<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\PredicatesApiResource as CommonPredicatesApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class PredicatesApiResource extends CommonPredicatesApiResource
{
    public function __construct(MetadataModelVersion $metadataModel)
    {
        parent::__construct($metadataModel);
    }
}
