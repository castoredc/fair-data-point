<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\Common\Dependency;

use App\Entity\DataSpecification\Common\Dependency\DependencyRule;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\Common\Collections\ArrayCollection;

class DependencyRuleFactory
{
    /**
     * @param array<mixed>               $data
     * @param ArrayCollection<Node>|null $nodes
     */
    public function createFromJson(array $data, ?ArrayCollection $nodes = null): DependencyRule
    {
        $rule = new DependencyRule(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setElementId($data['field']);

        if ($nodes !== null) {
            $rule->setElement($nodes->get($rule->getElementId()));
        }

        return $rule;
    }
}
