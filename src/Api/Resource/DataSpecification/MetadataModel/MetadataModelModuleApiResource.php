<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\Common\DataSpecificationModuleApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use function assert;

class MetadataModelModuleApiResource extends DataSpecificationModuleApiResource
{
    public function __construct(MetadataModelGroup $module, bool $groupTriples = true)
    {
        parent::__construct($module, $groupTriples);
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $return = parent::toArray();

        $module = $this->module;
        assert($module instanceof MetadataModelGroup);

        if ($this->groupTriples) {
            $return['groupedTriples'] = (new GroupedTriplesApiResource($module))->toArray();
        } else {
            $return['triples'] = (new TriplesApiResource($module))->toArray();
        }

        return $return;
    }
}
