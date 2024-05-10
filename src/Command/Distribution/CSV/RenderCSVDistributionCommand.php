<?php
declare(strict_types=1);

namespace App\Command\Distribution\CSV;

use App\Entity\Castor\Record;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\FAIRData\Catalog;

class RenderCSVDistributionCommand
{
    /** @param Record[] $records */
    public function __construct(private array $records, private CSVDistribution $distribution, private Catalog $catalog)
    {
    }

    /** @return Record[] */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function getDistribution(): CSVDistribution
    {
        return $this->distribution;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
