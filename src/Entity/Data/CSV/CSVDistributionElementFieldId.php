<?php
declare(strict_types=1);

namespace App\Entity\Data\CSV;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CSVDistributionElementFieldId extends CSVDistributionElement
{
    /** @ORM\Column(type="text") */
    private string $fieldId;

    public function __construct(string $fieldId)
    {
        $this->fieldId = $fieldId;
    }

    public function getFieldId(): string
    {
        return $this->fieldId;
    }
}
