<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelModule;

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
            'order' => $this->module->getOrder(),
            'groupedTriples' => (new GroupedTriplesApiResource($this->module))->toArray(),
        ];
    }
}
