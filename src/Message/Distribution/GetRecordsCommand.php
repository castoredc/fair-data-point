<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Security\CastorUser;

class GetRecordsCommand
{
    /** @var Distribution */
    private $distribution;

    /** @var Catalog */
    private $catalog;

    /** @var CastorUser|null */
    private $user;

    public function __construct(Distribution $distribution, Catalog $catalog, ?CastorUser $user)
    {
        $this->distribution = $distribution;
        $this->catalog = $catalog;
        $this->user = $user;
    }

    public function getDistribution(): Distribution
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
