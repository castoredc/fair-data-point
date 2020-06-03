<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\RDF\RDFDistributionPrefix;

class RDFDistributionPrefixResource implements ApiResource
{
    /** @var RDFDistributionPrefix */
    private $prefix;

    public function __construct(RDFDistributionPrefix $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->prefix->getId(),
            'prefix' => $this->prefix->getPrefix(),
            'uri' => $this->prefix->getUri()->getValue(),
        ];
    }
}
