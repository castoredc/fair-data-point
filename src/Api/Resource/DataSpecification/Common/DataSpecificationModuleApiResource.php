<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Group;
use function sprintf;

abstract class DataSpecificationModuleApiResource implements ApiResource
{
    protected Group $module;

    protected bool $groupTriples;

    public function __construct(Group $module, bool $groupTriples = true)
    {
        $this->module = $module;
        $this->groupTriples = $groupTriples;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->module->getId(),
            'title' => $this->module->getTitle(),
            'displayName' => sprintf('Module %d. %s', $this->module->getOrder(), $this->module->getTitle()),
            'order' => $this->module->getOrder(),
            'repeated' => $this->module->isRepeated(),
            'dependent' => $this->module->isDependent(),
            'dependencies' => $this->module->getDependencies() !== null ? (new DataSpecificationDependencyApiResource($this->module->getDependencies()))->toArray() : null,
        ];
    }
}
