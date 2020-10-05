<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFDistribution;

class RenderRDFDistributionCommand
{
    /** @var Record[] */
    private array $records;

    private RDFDistribution $distribution;

    /**
     * @param Record[] $records
     */
    public function __construct(array $records, RDFDistribution $distribution)
    {
        $this->records = $records;
        $this->distribution = $distribution;
    }

    /**
     * @return Record[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}
