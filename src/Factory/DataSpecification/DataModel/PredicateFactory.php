<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Predicate;
use App\Entity\Iri;

class PredicateFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(DataModelVersion $version, array $data): Predicate
    {
        return new Predicate($version, new Iri($data['value']['value']));
    }
}
