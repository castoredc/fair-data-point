<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Visualization;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\Triple;

class VisualizationEdgeApiResource implements ApiResource
{
    public function __construct(private Triple $triple)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $prefixes = $this->triple->getDataSpecificationVersion()->getPrefixes();

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
