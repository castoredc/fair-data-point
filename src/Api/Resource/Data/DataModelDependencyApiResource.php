<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Dependency\DataModelDependency;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;

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
        $array = [
            'id' => $this->dependency->getId(),
            'group' => $this->dependency->getGroup() !== null ? $this->dependency->getGroup()->getId() : null,
        ];

        if ($this->dependency instanceof DataModelDependencyGroup) {
            $array['combinator'] = $this->dependency->getCombinator()->toString();
            $array['rules'] = [];

            foreach ($this->dependency->getRules() as $rule) {
                $array['rules'][] = (new DataModelDependencyApiResource($rule))->toArray();
            }
        } elseif ($this->dependency instanceof DataModelDependencyRule) {
            $array['field'] = $this->dependency->getNode()->getId();
            $array['operator'] = $this->dependency->getOperator()->toString();
            $array['value'] = $this->dependency->getValue();
        }

        return $array;
    }
}
