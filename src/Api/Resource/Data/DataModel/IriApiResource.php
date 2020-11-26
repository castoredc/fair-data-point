<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Iri;

class IriApiResource implements ApiResource
{
    private DataModelVersion $dataModel;

    private Iri $iri;

    public function __construct(DataModelVersion $dataModel, Iri $iri)
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
