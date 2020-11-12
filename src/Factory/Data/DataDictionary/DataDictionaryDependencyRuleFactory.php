<?php
declare(strict_types=1);

namespace App\Factory\Data\DataDictionary;

use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyRule;
use App\Entity\Data\DataDictionary\Variable;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\Common\Collections\ArrayCollection;

class DataDictionaryDependencyRuleFactory
{
    /**
     * @param array<mixed>                   $data
     * @param ArrayCollection<Variable>|null $variables
     */
    public function createFromJson(array $data, ?ArrayCollection $variables = null): DataDictionaryDependencyRule
    {
        $rule = new DataDictionaryDependencyRule(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setVariableId($data['field']);

        if ($variables !== null) {
            $rule->setVariable($variables->get($rule->getVariableId()));
        }

        return $rule;
    }
}
