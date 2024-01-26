<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use const DATE_ATOM;

class DataDictionaryVersionApiResource implements ApiResource
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
            'id' => $this->dataDictionaryVersion->getId(),
            'version' => $this->dataDictionaryVersion->getVersion()->getValue(),
            'dataDictionary' => $this->dataDictionaryVersion->getDataDictionary()->getId(),
            'count' => [
                'groups' => $this->dataDictionaryVersion->getGroups()->count(),
            ],
            'createdAt' => $this->dataDictionaryVersion->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $this->dataDictionaryVersion->getUpdatedAt() !== null ? $this->dataDictionaryVersion->getUpdatedAt()->format(DATE_ATOM) : null,
        ];
    }
}
