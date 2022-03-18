<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataSpecification\Dependency\Dependency;
use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;
use App\Entity\Data\DataSpecification\Dependency\DependencyRule;
use function array_pop;

class DataDictionaryDependencyApiResource implements ApiResource
{
    private Dependency $dependency;

    public function __construct(Dependency $dependency)
    {
        $this->dependency = $dependency;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $description = [];

        $array = [
            'id' => $this->dependency->getId(),
            'group' => $this->dependency->getGroup() !== null ? $this->dependency->getGroup()->getId() : null,
        ];

        if ($this->dependency instanceof DependencyGroup) {
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
        } elseif ($this->dependency instanceof DependencyRule) {
            $array['field'] = $this->dependency->getElement()->getId();
            $array['operator'] = $this->dependency->getOperator()->toString();
            $array['value'] = $this->dependency->getValue();
            $array['description'] = $this->dependency->getElement()->getTitle() . ' ' . $array['operator'] . ' ' . $array['value'];

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
