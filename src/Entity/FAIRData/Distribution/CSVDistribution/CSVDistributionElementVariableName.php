<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\RDFDistribution;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CSVDistributionElementVariableName extends CSVDistributionElement
{
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $variableName;

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
