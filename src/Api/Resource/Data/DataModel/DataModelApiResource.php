<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use function assert;

class DataModelApiResource implements ApiResource
{
    private DataModel $dataModel;

    private bool $includeVersions;

    public function __construct(DataModel $dataModel, bool $includeVersions = true)
    {
        $this->dataModel = $dataModel;
        $this->includeVersions = $includeVersions;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $array = [
            'id' => $this->dataModel->getId(),
            'title' => $this->dataModel->getTitle(),
            'description' => $this->dataModel->getDescription(),
        ];

        if ($this->includeVersions) {
            $versions = [];

            foreach ($this->dataModel->getVersions() as $version) {
                assert($version instanceof DataModelVersion);

                $versions[] = (new DataModelVersionApiResource($version))->toArray();
            }

            $array['versions'] = $versions;
        }

        return $array;
    }
}
