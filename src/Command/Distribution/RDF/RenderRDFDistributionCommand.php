<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Castor\Record;
use App\Entity\Data\DistributionContents\RDFDistribution;

class RenderRDFDistributionCommand
{
    /** @param Record[] $records */
    public function __construct(private array $records, private RDFDistribution $distribution)
    {
    }

    /** @return Record[] */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}
