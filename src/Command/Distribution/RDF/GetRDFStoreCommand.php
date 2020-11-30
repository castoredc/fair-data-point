<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;

class GetRDFStoreCommand
{
    private RDFDistribution $distribution;

    public function __construct(RDFDistribution $distribution)
    {
        $this->distribution = $distribution;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}
