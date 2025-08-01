<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;

class DataDictionariesApiResource implements ApiResource
{
    /** @param DataDictionary[] $dataDictionaries */
    public function __construct(private array $dataDictionaries)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataDictionaries as $dataDictionary) {
            $data[] = (new DataDictionaryApiResource($dataDictionary))->toArray();
        }

        return $data;
    }
}
