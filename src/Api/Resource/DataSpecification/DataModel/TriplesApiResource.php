<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use function assert;

class TriplesApiResource implements ApiResource
{
    public function __construct(private DataModelGroup $module)
    {
    }

    /** @return array<mixed> */
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
