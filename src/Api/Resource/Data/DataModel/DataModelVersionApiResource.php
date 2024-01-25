<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use const DATE_ATOM;

class DataModelVersionApiResource implements ApiResource
{
    private DataModelVersion $dataModelVersion;

    public function __construct(DataModelVersion $dataModelVersion)
    {
        $this->dataModelVersion = $dataModelVersion;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->dataModelVersion->getId(),
            'version' => $this->dataModelVersion->getVersion()->getValue(),
            'dataModel' => $this->dataModelVersion->getDataModel()->getId(),
            'count' => [
                'modules' => $this->dataModelVersion->getGroups()->count(),
                'nodes' => $this->dataModelVersion->getElements()->count(),
            ],
            'createdAt' => $this->dataModelVersion->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $this->dataModelVersion->getUpdatedAt() !== null ? $this->dataModelVersion->getUpdatedAt()->format(DATE_ATOM) : null,
        ];
    }
}
