<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\Common\Dependency;

use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\DependencyCombinatorType;
use Doctrine\Common\Collections\ArrayCollection;
use function array_key_exists;

class DependencyGroupFactory
{
    public function __construct(private DependencyRuleFactory $dataModelDependencyRuleFactory)
    {
    }

    /**
     * @param array<mixed>               $data
     * @param ArrayCollection<Node>|null $nodes
     */
    public function createFromJson(array $data, ?ArrayCollection $nodes = null): DependencyGroup
    {
        $group = new DependencyGroup(DependencyCombinatorType::fromString($data['combinator']));

        foreach ($data['rules'] as $rule) {
            if (array_key_exists('combinator', $rule)) {
                // Is Group
                $newRule = $this->createFromJson($rule, $nodes);
            } else {
                // Is Rule
                $newRule = $this->dataModelDependencyRuleFactory->createFromJson($rule, $nodes);
            }

            $newRule->setGroup($group);
            $group->addRule($newRule);
        }

        return $group;
    }
}
