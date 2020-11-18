<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\Triple;
use function assert;

class TriplesApiResource implements ApiResource
{
    private DataModelGroup $module;

    public function __construct(DataModelGroup $module)
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
            assert($triple instanceof Triple);
            $data[] = (new TripleApiResource($triple))->toArray();
        }

        return $data;
    }
}
