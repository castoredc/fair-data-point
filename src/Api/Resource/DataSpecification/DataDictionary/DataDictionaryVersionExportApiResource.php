<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;

class DataDictionaryVersionExportApiResource implements ApiResource
{
    private DataDictionaryVersion $dataDictionaryVersion;

    public function __construct(DataDictionaryVersion $dataDictionaryVersion)
    {
        $this->dataDictionaryVersion = $dataDictionaryVersion;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'dataDictionary' => (new DataDictionaryApiResource($this->dataDictionaryVersion->getDataDictionary(), false))->toArray(),
            'version' => (new DataDictionaryVersionApiResource($this->dataDictionaryVersion))->toArray(),
            'groups' => (new DataDictionaryGroupsApiResource($this->dataDictionaryVersion))->toArray(),
        ];
    }
}
