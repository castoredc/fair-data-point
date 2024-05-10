<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;

class GetRDFFromStoreCommand
{
    public function __construct(private RDFDistribution $distribution, private ?string $record = null)
    {
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getRecord(): ?string
    {
        return $this->record;
    }
}
