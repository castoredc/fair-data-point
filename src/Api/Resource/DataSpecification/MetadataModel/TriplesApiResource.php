<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;

class TriplesApiResource implements ApiResource
{
    public function __construct(private MetadataModelGroup $module)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->module->getTriples() as $triple) {
            $data[] = (new TripleApiResource($triple))->toArray();
        }

        return $data;
    }
}
