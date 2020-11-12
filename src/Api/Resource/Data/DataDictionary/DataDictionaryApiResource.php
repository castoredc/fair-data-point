<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataDictionary\DataDictionary;

class DataDictionaryApiResource implements ApiResource
{
    private DataDictionary $dataDictionary;

    private bool $includeVersions;

    public function __construct(DataDictionary $dataDictionary, bool $includeVersions = true)
    {
        $this->dataDictionary = $dataDictionary;
        $this->includeVersions = $includeVersions;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [
            'id' => $this->dataDictionary->getId(),
            'title' => $this->dataDictionary->getTitle(),
            'description' => $this->dataDictionary->getDescription(),
        ];

        if ($this->includeVersions) {
            $versions = [];

            foreach ($this->dataDictionary->getVersions() as $version) {
                $versions[] = (new DataDictionaryVersionApiResource($version))->toArray();
            }

            $array['versions'] = $versions;
        }

        return $array;
    }
}
