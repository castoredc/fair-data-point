<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class CreateDataModelNodeMappingCommand extends CreateDataModelMappingCommand
{
    /** @param string[] $elements */
    public function __construct(
        RDFDistribution $distribution,
        private string $node,
        private array $elements,
        private bool $transform,
        private ?string $transformSyntax,
        DataModelVersion $dataModelVersion,
    ) {
        parent::__construct($distribution, $dataModelVersion);
    }

    public function getNode(): string
    {
        return $this->node;
    }

    /** @return string[] */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function isTransform(): bool
    {
        return $this->transform;
    }

    public function getTransformSyntax(): ?string
    {
        return $this->transformSyntax;
    }
}
