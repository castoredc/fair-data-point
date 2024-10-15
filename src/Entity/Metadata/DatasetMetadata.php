<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

#[ORM\Table(name: 'metadata_dataset')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DatasetMetadata extends Metadata
{
    #[ORM\JoinColumn(name: 'dataset', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Dataset::class, inversedBy: 'metadata')]
    private Dataset $dataset;

    /**
     *
     * @var Collection<OntologyConcept>
     */
    #[ORM\JoinTable(name: 'metadata_dataset_themes')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\Terminology\OntologyConcept::class, cascade: ['persist'])]
    private Collection $themes;

    #[ORM\JoinColumn(name: 'keyword', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: \App\Entity\FAIRData\LocalizedText::class, cascade: ['persist'])]
    private ?LocalizedText $keyword = null;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;

        $this->values = new ArrayCollection();
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

    /** @return Collection<OntologyConcept> */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    /** @param Collection<OntologyConcept> $themes */
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

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return $this->dataset;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::dataset();
    }
}
