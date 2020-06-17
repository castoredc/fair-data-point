<?php

namespace App\Message\Distribution;

use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class CreateDataModelMappingCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var string */
    private $node;

    /** @var string */
    private $element;

    public function __construct(RDFDistribution $distribution, string $node, string $element)
    {
        $this->distribution = $distribution;
        $this->node = $node;
        $this->element = $element;
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
}