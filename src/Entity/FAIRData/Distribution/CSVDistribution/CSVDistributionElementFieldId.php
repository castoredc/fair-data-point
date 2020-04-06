<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\RDFDistribution;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CSVDistributionElementFieldId extends CSVDistributionElement
{
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $fieldId;

    /**
     * @return string
     */
    public function getFieldId(): string
    {
        return $this->fieldId;
    }
}
