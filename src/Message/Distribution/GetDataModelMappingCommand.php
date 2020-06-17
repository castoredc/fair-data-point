<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Catalog;
use App\Security\CastorUser;

class GetDataModelMappingCommand
{
    /** @var RDFDistribution */
    private $distribution;

    public function __construct(RDFDistribution $distribution)
    {
        $this->distribution = $distribution;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}
