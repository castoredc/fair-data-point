<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DistributionContents;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents
{
    public function getRDFUrl(): string
    {
        return $this->getDistribution()->getAccessUrl() . '/rdf';
    }
}
