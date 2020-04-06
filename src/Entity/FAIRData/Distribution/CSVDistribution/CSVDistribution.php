<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\CSVDistribution;

use App\Entity\Castor\Form\Field;
use App\Entity\FAIRData\Distribution\Distribution;
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

    /**
     * @return Collection<string, CSVDistributionElement>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function isIncludeAll(): bool
    {
        return $this->includeAll;
    }

    public function isFieldIncluded(Field $field): bool
    {
        if ($this->includeAll === true) {
            return true;
        }

        foreach ($this->elements as $element) {
            if ($element instanceof CSVDistributionElementFieldId && $element->getFieldId() === $field->getId()) {
                return true;
            }

            if ($element instanceof CSVDistributionElementVariableName && $element->getVariableName() === $field->getVariableName()) {
                return true;
            }
        }

        return false;
    }

    public function getAccessUrl(): string
    {
        return parent::getAccessUrl() . '/csv';
    }
}
