<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\RDF\RDFDistribution;

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
