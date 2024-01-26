<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\Triple;
use Doctrine\Common\Collections\ArrayCollection;

class TripleFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(MetadataModelGroup $module, ArrayCollection $nodes, ArrayCollection $predicates, array $data): Triple
    {
        return new Triple(
            $module,
            $nodes->get($data['subject']['id']),
            $predicates->get($data['predicate']['id']),
            $nodes->get($data['object']['id'])
        );
    }
}
