<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use function assert;

class DataDictionaryGroupsApiResource implements ApiResource
{
    public function __construct(private DataDictionaryVersion $dataDictionary)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataDictionary->getGroups() as $group) {
            assert($group instanceof DataDictionaryGroup);

            $data[] = (new DataDictionaryGroupApiResource($group))->toArray();
        }

        return $data;
    }
}
