<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;

class DataModelPrefixApiResource implements ApiResource
{
    private NamespacePrefix $prefix;

    public function __construct(NamespacePrefix $prefix)
    {
        $this->prefix = $prefix;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->prefix->getId(),
            'prefix' => $this->prefix->getPrefix(),
            'uri' => $this->prefix->getUri()->getValue(),
        ];
    }
}
