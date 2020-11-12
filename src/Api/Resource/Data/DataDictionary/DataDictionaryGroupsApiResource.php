<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;

class DataDictionaryGroupsApiResource implements ApiResource
{
    private DataDictionaryVersion $dataDictionary;

    public function __construct(DataDictionaryVersion $dataDictionary)
    {
        $this->dataDictionary = $dataDictionary;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataDictionary->getGroups() as $group) {
            $data[] = (new DataDictionaryGroupApiResource($group))->toArray();
        }

        return $data;
    }
}
