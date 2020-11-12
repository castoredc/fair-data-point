<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependency;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyGroup;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyRule;
use function array_pop;

class DataDictionaryDependencyApiResource implements ApiResource
{
    private DataDictionaryDependency $dependency;

    public function __construct(DataDictionaryDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $description = [];

        $array = [
            'id' => $this->dependency->getId(),
            'group' => $this->dependency->getGroup() !== null ? $this->dependency->getGroup()->getId() : null,
        ];

        if ($this->dependency instanceof DataDictionaryDependencyGroup) {
            $array['combinator'] = $this->dependency->getCombinator()->toString();
            $array['rules'] = [];

            foreach ($this->dependency->getRules() as $rule) {
                $newRules = (new DataDictionaryDependencyApiResource($rule))->toArray();
                $array['rules'][] = $newRules;

                $description[] = [
                    'type' => 'group',
                    'rules' => $newRules['description'],
                ];

                $description[] = [
                    'type' => 'combinator',
                    'text' => $array['combinator'],
                ];
            }

            array_pop($description);
        } elseif ($this->dependency instanceof DataDictionaryDependencyRule) {
            $array['field'] = $this->dependency->getVariable()->getId();
            $array['operator'] = $this->dependency->getOperator()->toString();
            $array['value'] = $this->dependency->getValue();
            $array['description'] = $this->dependency->getVariable()->getTitle() . ' ' . $array['operator'] . ' ' . $array['value'];

            $description[] = [
                'type' => 'rule',
                'text' => $array['description'],
            ];
        }

        // $array['description'] = implode(' ', $description);
        $array['description'] = $description;

        return $array;
    }
}
