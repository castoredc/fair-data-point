<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\DataSpecification\Common\PredicateApiResource as CommonPredicateApiResource;
use App\Entity\DataSpecification\DataModel\Predicate;

class PredicateApiResource extends CommonPredicateApiResource
{
    public function __construct(Predicate $predicate)
    {
        parent::__construct($predicate);
    }
}
