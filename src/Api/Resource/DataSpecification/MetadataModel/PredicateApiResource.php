<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\PredicateApiResource as CommonPredicateApiResource;
use App\Entity\DataSpecification\MetadataModel\Predicate;

class PredicateApiResource extends CommonPredicateApiResource
{
    public function __construct(Predicate $predicate)
    {
        parent::__construct($predicate);
    }
}
