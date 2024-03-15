<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\Iri;

class PredicateFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(MetadataModelVersion $version, array $data): Predicate
    {
        return new Predicate($version, new Iri($data['value']['value']));
    }
}
