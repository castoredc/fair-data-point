<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Iri;
use function assert;

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
            assert($prefix instanceof NamespacePrefix);
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
