<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Dependency\Dependency;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\Common\Dependency\DependencyRule;
use function array_pop;

class DataSpecificationDependencyApiResource implements ApiResource
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
