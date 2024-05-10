<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\Iri;

class IriApiResource implements ApiResource
{
    public function __construct(private ModelVersion $dataSpecification, private Iri $iri)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $prefixes = $this->dataSpecification->getPrefixes();

        $iriPrefix = $this->iri->getPrefix();
        $prefixLabel = null;
        $prefixedValue = null;

        foreach ($prefixes as $prefix) {
            if ($prefix->getUri()->getValue() !== $iriPrefix) {
                continue;
            }

            $prefixLabel = $prefix->getPrefix();
            $prefixedValue = $prefixLabel . ':' . $this->iri->getBase();
        }

        return [
            'value' => $this->iri->getValue(),
            'prefixLabel' => $prefixLabel,
            'prefixedValue' => $prefixedValue,
            'base' => $this->iri->getBase(),
        ];
    }
}
