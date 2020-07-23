<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Dependency\DataModelDependency;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;
use function array_pop;

class DataModelDependencyApiResource implements ApiResource
{
    /** @var DataModelDependency */
    private $dependency;

    public function __construct(DataModelDependency $dependency)
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

        if ($this->dependency instanceof DataModelDependencyGroup) {
            $array['combinator'] = $this->dependency->getCombinator()->toString();
            $array['rules'] = [];

            foreach ($this->dependency->getRules() as $rule) {
                $newRules = (new DataModelDependencyApiResource($rule))->toArray();
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
        } elseif ($this->dependency instanceof DataModelDependencyRule) {
            $array['field'] = $this->dependency->getNode()->getId();
            $array['operator'] = $this->dependency->getOperator()->toString();
            $array['value'] = $this->dependency->getValue();
            $array['description'] = $this->dependency->getNode()->getTitle() . ' ' . $array['operator'] . ' ' . $array['value'];

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
