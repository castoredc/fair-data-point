<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\RDF\RDFDistribution;

class GetRDFStoreCommand
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
