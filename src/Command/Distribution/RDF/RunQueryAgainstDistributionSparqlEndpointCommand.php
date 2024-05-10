<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;

class RunQueryAgainstDistributionSparqlEndpointCommand
{
    public function __construct(private RDFDistribution $distribution, private string $query)
    {
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
