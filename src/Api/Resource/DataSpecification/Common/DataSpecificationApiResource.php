<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\DataSpecification;

abstract class DataSpecificationApiResource implements ApiResource
{
    private DataSpecification $dataSpecification;

    private bool $includeVersions;

    public function __construct(DataSpecification $dataSpecification, bool $includeVersions = true)
    {
        $this->dataSpecification = $dataSpecification;
        $this->includeVersions = $includeVersions;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $array = [
            'id' => $this->dataSpecification->getId(),
            'title' => $this->dataSpecification->getTitle(),
            'description' => $this->dataSpecification->getDescription(),
        ];

        if ($this->includeVersions) {
            $versions = [];

            foreach ($this->dataSpecification->getVersions() as $version) {
                $versions[] = (new DataSpecificationVersionApiResource($version))->toArray();
            }

            $array['versions'] = $versions;
        }

        return $array;
    }
}
