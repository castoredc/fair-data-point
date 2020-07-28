<?php
declare(strict_types=1);

namespace App\Factory\Data\DataModel;

use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\Common\Collections\ArrayCollection;

class DataModelDependencyRuleFactory
{
    /**
     * @param array<mixed>               $data
     * @param ArrayCollection<Node>|null $nodes
     */
    public function createFromJson(array $data, ?ArrayCollection $nodes = null): DataModelDependencyRule
    {
        $rule = new DataModelDependencyRule(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setNodeId($data['field']);

        if ($nodes !== null) {
            $rule->setNode($nodes->get($rule->getNodeId()));
        }

        return $rule;
    }
}
