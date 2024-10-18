<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DistributionContents\Dependency\Dependency;
use App\Entity\Data\DistributionContents\Dependency\DependencyGroup;
use App\Entity\Data\DistributionContents\Dependency\DependencyRule;
use function array_pop;

class DistributionContentsDependencyApiResource implements ApiResource
{
    public function __construct(private Dependency $dependency)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $description = [];

        $array = [
            'id' => $this->dependency->getId(),
            'group' => $this->dependency->getGroup()?->getId(),
        ];

        if ($this->dependency instanceof DependencyGroup) {
            $array['combinator'] = $this->dependency->getCombinator()->toString();
            $array['rules'] = [];

            foreach ($this->dependency->getRules() as $rule) {
                $newRules = (new self($rule))->toArray();
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
            $array['operator'] = $this->dependency->getOperator()->toString();
            $array['value'] = $this->dependency->getValue();
            $array['description'] = '';

            if ($this->dependency->getType()->isValueNode()) {
                $array['field'] = $this->dependency->getNode()->getId();
                $array['fieldDetails'] = [
                    'type' => 'valueNode',
                    'value' => $this->dependency->getNode()->getId(),
                    'name' => $this->dependency->getNode()->getId(),
                    'label' => $this->dependency->getNode()->getTitle(),
                    'valueType' => $this->dependency->getNode()->getType()->toString(),
                ];
                $array['description'] = $this->dependency->getNode()->getTitle() . ' ' . $array['operator'] . ' ' . $array['value'];
            } elseif ($this->dependency->getType()->isInstitute()) {
                $array['field'] = $this->dependency->getType()->toString();
                $array['fieldDetails'] = [
                    'type' => 'recordDetails',
                    'value' => $this->dependency->getType()->toString(),
                    'name' => $this->dependency->getType()->toString(),
                    'label' => 'Institute',
                    'valueType' => $this->dependency->getType()->toString(),
                ];
                $array['description'] = 'Institute ' . $array['operator'] . ' ' . $array['value'];
            }

            $description[] = [
                'type' => 'rule',
                'text' => $array['description'],
            ];
        }

        $array['description'] = $description;

        return $array;
    }
}
