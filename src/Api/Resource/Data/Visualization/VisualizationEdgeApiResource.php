<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\Visualization;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Triple;

class VisualizationEdgeApiResource implements ApiResource
{
    /** @var Triple */
    private $triple;

    public function __construct(Triple $triple)
    {
        $this->triple = $triple;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $prefixes = $this->triple->getModule()->getDataModel()->getPrefixes();

        $predicateIri = $this->triple->getPredicate()->getIri();

        $iriPrefix = $predicateIri->getPrefix();
        $prefixedValue = $predicateIri->getBase();

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
            if ($prefix->getUri()->getValue() !== $iriPrefix) {
                continue;
            }

            $prefixedValue = $prefix->getPrefix() . ':' . $predicateIri->getBase();
        }

        return [
            'from' => $this->triple->getSubject()->getId(),
            'to' => $this->triple->getObject()->getId(),
            'label' => $prefixedValue,
            'arrows' => 'to'
        ];
    }
}
