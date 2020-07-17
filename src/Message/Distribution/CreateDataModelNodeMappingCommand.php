<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\RDF\RDFDistribution;

class CreateDataModelNodeMappingCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var string */
    private $node;

    /** @var string */
    private $element;

    /** @var DataModelVersion */
    private $dataModelVersion;

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
