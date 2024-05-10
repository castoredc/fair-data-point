<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Version;
use const DATE_ATOM;

class DataSpecificationVersionApiResource implements ApiResource
{
    public function __construct(private Version $dataSpecificationVersion)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->dataSpecificationVersion->getId(),
            'version' => $this->dataSpecificationVersion->getVersion()->getValue(),
            'dataSpecification' => $this->dataSpecificationVersion->getDataSpecification()->getId(),
            'count' => [
                'modules' => $this->dataSpecificationVersion->getGroups()->count(),
                'nodes' => $this->dataSpecificationVersion->getElements()->count(),
            ],
            'createdAt' => $this->dataSpecificationVersion->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $this->dataSpecificationVersion->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
