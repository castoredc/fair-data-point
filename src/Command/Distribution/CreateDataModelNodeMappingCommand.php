<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\RDF\RDFDistribution;

class CreateDataModelNodeMappingCommand
{
    private RDFDistribution $distribution;

    private string $node;

    private string $element;

    private DataModelVersion $dataModelVersion;

    public function __construct(RDFDistribution $distribution, string $node, string $element, DataModelVersion $dataModelVersion)
    {
        $this->distribution = $distribution;
        $this->node = $node;
        $this->element = $element;
        $this->dataModelVersion = $dataModelVersion;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
