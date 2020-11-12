<?php
declare(strict_types=1);

namespace App\Factory\Data\DataDictionary;

use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyGroup;
use App\Entity\Data\DataDictionary\Variable;
use App\Entity\Enum\DependencyCombinatorType;
use Doctrine\Common\Collections\ArrayCollection;
use function array_key_exists;

class DataDictionaryDependencyGroupFactory
{
    private DataDictionaryDependencyRuleFactory $dataDictionaryDependencyRuleFactory;

    public function __construct(DataDictionaryDependencyRuleFactory $dataDictionaryDependencyRuleFactory)
    {
        $this->dataDictionaryDependencyRuleFactory = $dataDictionaryDependencyRuleFactory;
    }

    /**
     * @param array<mixed>                   $data
     * @param ArrayCollection<Variable>|null $variables
     */
    public function createFromJson(array $data, ?ArrayCollection $variables = null): DataDictionaryDependencyGroup
    {
        $group = new DataDictionaryDependencyGroup(DependencyCombinatorType::fromString($data['combinator']));

        foreach ($data['rules'] as $rule) {
            if (array_key_exists('combinator', $rule)) {
                // Is Group
                $newRule = $this->createFromJson($rule, $variables);
            } else {
                // Is Rule
                $newRule = $this->dataDictionaryDependencyRuleFactory->createFromJson($rule, $variables);
            }

            $newRule->setGroup($group);
            $group->addRule($newRule);
        }

        return $group;
    }
}
