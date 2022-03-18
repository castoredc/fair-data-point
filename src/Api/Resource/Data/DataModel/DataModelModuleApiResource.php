<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelGroup;
use function sprintf;

class DataModelModuleApiResource implements ApiResource
{
    private DataModelGroup $module;

    private bool $groupTriples;

    public function __construct(DataModelGroup $module, bool $groupTriples = true)
    {
        $this->module = $module;
        $this->groupTriples = $groupTriples;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $return = [
            'id' => $this->module->getId(),
            'title' => $this->module->getTitle(),
            'displayName' => sprintf('Module %d. %s', $this->module->getOrder(), $this->module->getTitle()),
            'order' => $this->module->getOrder(),
            'repeated' => $this->module->isRepeated(),
            'dependent' => $this->module->isDependent(),
            'dependencies' => $this->module->getDependencies() !== null ? (new DataModelDependencyApiResource($this->module->getDependencies()))->toArray() : null,
        ];

        if ($this->groupTriples) {
            $return['groupedTriples'] = (new GroupedTriplesApiResource($this->module))->toArray();
        } else {
            $return['triples'] = (new TriplesApiResource($this->module))->toArray();
        }

        return $return;
    }
}
