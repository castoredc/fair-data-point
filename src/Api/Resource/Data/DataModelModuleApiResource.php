<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use function sprintf;

class DataModelModuleApiResource implements ApiResource
{
    /** @var DataModelModule */
    private $module;

    public function __construct(DataModelModule $module)
    {
        $this->module = $module;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->module->getId(),
            'title' => $this->module->getTitle(),
            'displayName' => sprintf('Module %d. %s', $this->module->getOrder(), $this->module->getTitle()),
            'order' => $this->module->getOrder(),
            'repeated' => $this->module->isRepeated(),
            'dependent' => $this->module->isDependent(),
            'dependencies' => $this->module->getDependencies() !== null ? (new DataModelDependencyApiResource($this->module->getDependencies()))->toArray() : null,
            'groupedTriples' => (new GroupedTriplesApiResource($this->module))->toArray(),
        ];
    }
}
