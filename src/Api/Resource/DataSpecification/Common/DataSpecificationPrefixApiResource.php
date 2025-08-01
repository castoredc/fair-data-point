<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\NamespacePrefix;

class DataSpecificationPrefixApiResource implements ApiResource
{
    public function __construct(private NamespacePrefix $prefix)
    {
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
