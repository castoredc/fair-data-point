<?php
declare(strict_types=1);

namespace App\Factory\Data\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use Doctrine\Common\Collections\ArrayCollection;

class TripleFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(DataModelGroup $module, ArrayCollection $nodes, ArrayCollection $predicates, array $data): Triple
    {
        return new Triple(
            $module,
            $nodes->get($data['subject']['id']),
            $predicates->get($data['predicate']['id']),
            $nodes->get($data['object']['id'])
        );
    }
}
