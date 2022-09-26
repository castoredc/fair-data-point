<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;

class RunQueryAgainstDistributionSparqlEndpointCommand
{
    private RDFDistribution $distribution;
    private string $query;

    public function __construct(RDFDistribution $distribution, string $query)
    {
        $this->distribution = $distribution;
        $this->query = $query;
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
