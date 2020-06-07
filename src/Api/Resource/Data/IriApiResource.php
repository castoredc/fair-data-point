<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Iri;

class IriApiResource implements ApiResource
{
    /** @var DataModel */
    private $dataModel;

    /** @var Iri */
    private $iri;

    public function __construct(DataModel $dataModel, Iri $iri)
    {
        $this->dataModel = $dataModel;
        $this->iri = $iri;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $prefixes = $this->dataModel->getPrefixes();

        $iriPrefix = $this->iri->getPrefix();
        $prefixLabel = null;
        $prefixedValue = null;

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
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
