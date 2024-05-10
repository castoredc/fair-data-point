<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\ModelVersion;

class DataSpecificationPrefixesApiResource implements ApiResource
{
    public function __construct(private ModelVersion $dataSpecification)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataSpecification->getPrefixes() as $prefix) {
            $data[] = (new DataSpecificationPrefixApiResource($prefix))->toArray();
        }

        return $data;
    }
}
