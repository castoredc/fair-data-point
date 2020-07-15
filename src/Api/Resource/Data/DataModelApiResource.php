<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModel;

class DataModelApiResource implements ApiResource
{
    /** @var DataModel */
    private $dataModel;

    public function __construct(DataModel $dataModel)
    {
        $this->dataModel = $dataModel;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $versions = [];

        foreach($this->dataModel->getVersions() as $version)
        {
            $versions[] = (new DataModelVersionApiResource($version))->toArray();
        }

        return [
            'id' => $this->dataModel->getId(),
            'title' => $this->dataModel->getTitle(),
            'description' => $this->dataModel->getDescription(),
            'versions' => $versions
        ];
    }
}
