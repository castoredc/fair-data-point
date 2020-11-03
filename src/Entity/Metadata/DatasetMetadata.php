<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_dataset")
 * @ORM\HasLifecycleCallbacks
 */
class DatasetMetadata extends Metadata
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="metadata", fetch="EAGER")
     * @ORM\JoinColumn(name="dataset", referencedColumnName="id", nullable=FALSE)
     */
    private Dataset $dataset;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Terminology\OntologyConcept",cascade={"persist"})
     * @ORM\JoinTable(name="metadata_dataset_themes")
     *
     * @var Collection<OntologyConcept>
     */
    private Collection $themes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="keyword", referencedColumnName="id")
     */
    private ?LocalizedText $keyword = null;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function setDataset(Dataset $dataset): void
    {
        $this->dataset = $dataset;
    }

    public function hasThemes(): bool
    {
        return count($this->themes) > 0;
    }

    /**
     * @return Collection<OntologyConcept>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    /**
     * @param Collection<OntologyConcept> $themes
     */
    public function setThemes(Collection $themes): void
    {
        $this->themes = $themes;
    }

    public function addTheme(OntologyConcept $theme): void
    {
        $this->themes->add($theme);
    }

    public function removeTheme(OntologyConcept $theme): void
    {
        $this->themes->removeElement($theme);
    }

    public function getKeyword(): ?LocalizedText
    {
        return $this->keyword;
    }

    public function setKeyword(?LocalizedText $keyword): void
    {
        $this->keyword = $keyword;
    }
}
