<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\CSVDistribution;

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

    public function __construct(string $fieldId)
    {
        $this->fieldId = $fieldId;
    }

    public function getFieldId(): string
    {
        return $this->fieldId;
    }
}
