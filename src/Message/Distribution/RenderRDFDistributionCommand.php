<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Record;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;
use App\Security\CastorUser;

class RenderRDFDistributionCommand
{
    /** @var Record[] */
    private $records;

    /** @var RDFDistribution */
    private $distribution;

    /** @var CastorUser */
    private $user;

    /**
     * @param Record[] $records
     */
    public function __construct(array $records, RDFDistribution $distribution, CastorUser $user)
    {
        $this->records = $records;
        $this->distribution = $distribution;
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

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
