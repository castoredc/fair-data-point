<?php
declare(strict_types=1);

namespace App\Entity\Data\CSV;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CSVDistributionElementVariableName extends CSVDistributionElement
{
    /** @ORM\Column(type="text") */
    private string $variableName;

    public function __construct(string $variableName)
    {
        $this->variableName = $variableName;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
