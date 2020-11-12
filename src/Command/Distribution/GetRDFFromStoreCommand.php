<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DistributionContents\RDFDistribution;

class GetRDFFromStoreCommand
{
    private RDFDistribution $distribution;

    private ?string $record = null;

    public function __construct(RDFDistribution $distribution, ?string $record)
    {
        $this->distribution = $distribution;
        $this->record = $record;
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
