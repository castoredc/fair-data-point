<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Visualization;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\Triple;

class VisualizationEdgeApiResource implements ApiResource
{
    private Triple $triple;

    public function __construct(Triple $triple)
    {
        $this->triple = $triple;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $prefixes = $this->triple->getDataModelVersion()->getPrefixes();

        $predicateIri = $this->triple->getPredicate()->getIri();

        $iriPrefix = $predicateIri->getPrefix();
        $prefixedValue = $predicateIri->getBase();

        foreach ($prefixes as $prefix) {
            if ($prefix->getUri()->getValue() !== $iriPrefix) {
                continue;
            }

            $prefixedValue = $prefix->getPrefix() . ':' . $predicateIri->getBase();
        }

        return [
            'from' => $this->triple->getSubject()->getId(),
            'to' => $this->triple->getObject()->getId(),
            'label' => $prefixedValue,
            'arrows' => 'to',
        ];
    }
}
