<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelModule;

class TriplesApiResource implements ApiResource
{
    private DataModelModule $module;

    public function __construct(DataModelModule $module)
    {
        $this->module = $module;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->module->getTriples() as $triple) {
            $data[] = (new TripleApiResource($triple))->toArray();
        }

        return $data;
    }
}
