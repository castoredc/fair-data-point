<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Record;
use App\Entity\FAIRData\Catalog;
use App\Entity\Data\RDF\RDFDistribution;
use App\Security\CastorUser;

class RenderRDFDistributionCommand
{
    /** @var Record[] */
    private $records;

    /** @var RDFDistribution */
    private $distribution;

    /** @var Catalog */
    private $catalog;

    /** @var CastorUser|null */
    private $user;

    /**
     * @param Record[] $records
     */
    public function __construct(array $records, RDFDistribution $distribution, Catalog $catalog, ?CastorUser $user)
    {
        $this->records = $records;
        $this->distribution = $distribution;
        $this->catalog = $catalog;
        $this->user = $user;
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

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    public function getUser(): ?CastorUser
    {
        return $this->user;
    }
}
