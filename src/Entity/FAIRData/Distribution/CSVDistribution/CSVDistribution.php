<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\RDFDistribution;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_csv")
 */
class CSVDistribution extends Distribution
{
    /**
     * @ORM\OneToMany(targetEntity="CSVDistributionElement", mappedBy="distribution",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="elements", referencedColumnName="id")
     *
     * @var Collection<string, CSVDistributionElement>
     */
    private $elements;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $includeAll = false;

    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function isIncludeAll(): bool
    {
        return $this->includeAll;
    }
}
