<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;

class DataModelVersionApiResource implements ApiResource
{
    /** @var DataModelVersion */
    private $dataModelVersion;

    public function __construct(DataModelVersion $dataModelVersion)
    {
        $this->dataModelVersion = $dataModelVersion;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->dataModelVersion->getId(),
            'version' => $this->dataModelVersion->getVersion()->getValue(),
            'dataModel' => $this->dataModelVersion->getDataModel()->getId()
        ];
    }
}
