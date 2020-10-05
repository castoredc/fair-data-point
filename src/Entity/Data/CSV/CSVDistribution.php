<?php
declare(strict_types=1);

namespace App\Entity\Data\CSV;

use App\Entity\Castor\Form\Field;
use App\Entity\Data\DistributionContents;
use App\Entity\FAIRData\Distribution;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_csv")
 */
class CSVDistribution extends DistributionContents
{
    /**
     * @ORM\OneToMany(targetEntity="CSVDistributionElement", mappedBy="distribution",cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     * @ORM\JoinColumn(name="elements", referencedColumnName="id")
     *
     * @var Collection<string, CSVDistributionElement>
     */
    private Collection $elements;

    /** @ORM\Column(type="boolean") */
    private bool $includeAll = false;

    /** @inheritDoc */
    public function __construct(Distribution $distribution, int $accessRights, bool $isPublished, bool $includeAll)
    {
        parent::__construct($distribution, $accessRights, $isPublished);

        $this->elements = new ArrayCollection();
        $this->includeAll = $includeAll;
    }

    /**
     * @return Collection<string, CSVDistributionElement>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    /**
     * @param Collection<string, CSVDistributionElement> $elements
     */
    public function setElements(Collection $elements): void
    {
        $this->elements = $elements;
    }

    public function addElement(CSVDistributionElement $element): void
    {
        $element->setDistribution($this);
        $this->elements->add($element);
    }

    public function setIncludeAll(bool $includeAll): void
    {
        $this->includeAll = $includeAll;
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

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/csv';
    }
}
