<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DistributionContents\RDFDistribution;

class CreateDataModelNodeMappingCommand extends CreateDataModelMappingCommand
{
    private string $node;

    /** @var string[] */
    private array $elements;

    private bool $transform;

    private ?string $transformSyntax;

    /** @param string[] $elements */
    public function __construct(
        RDFDistribution $distribution,
        string $node,
        array $elements,
        bool $transform,
        ?string $transformSyntax,
        DataModelVersion $dataModelVersion
    ) {
        parent::__construct($distribution, $dataModelVersion);

        $this->node = $node;
        $this->elements = $elements;
        $this->transform = $transform;
        $this->transformSyntax = $transformSyntax;
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
