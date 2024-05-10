<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use function assert;

class DataDictionaryApiResource implements ApiResource
{
    public function __construct(private DataDictionary $dataDictionary, private bool $includeVersions = true)
    {
    }

    /** @return array<mixed> */
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
                assert($version instanceof DataDictionaryVersion);
                $versions[] = (new DataDictionaryVersionApiResource($version))->toArray();
            }

            $array['versions'] = $versions;
        }

        return $array;
    }
}
