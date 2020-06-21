<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Record;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\Catalog;

class RenderCSVDistributionCommand
{
    /** @var Record[] */
    private $records;

    /** @var CSVDistribution */
    private $distribution;

    /** @var Catalog */
    private $catalog;

    /**
     * @param Record[] $records
     */
    public function __construct(array $records, CSVDistribution $distribution, Catalog $catalog)
    {
        $this->records = $records;
        $this->distribution = $distribution;
        $this->catalog = $catalog;
    }

    /**
     * @return Record[]
     */
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
