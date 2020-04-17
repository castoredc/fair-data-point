<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\CSVDistribution;

use App\Entity\Castor\Form\Field;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="CSVDistributionElement", mappedBy="distribution",cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
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

    /** @inheritDoc */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, int $accessRights, bool $includeAll, Dataset $dataset)
    {
        parent::__construct($slug, $title, $version, $description, $publishers, $language, $license, $accessRights, $dataset);

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

    public function getAccessUrl(): string
    {
        return parent::getAccessUrl() . '/csv';
    }
}
