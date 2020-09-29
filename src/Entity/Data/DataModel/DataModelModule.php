<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_module")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelModule
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(name="`order`", type="integer") */
    private int $order;

    /**
     * @ORM\ManyToOne(targetEntity="DataModelVersion", inversedBy="modules",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModel;

    /**
     * @ORM\OneToMany(targetEntity="Triple", mappedBy="module", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<string, Triple>
     */
    private Collection $triples;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isRepeated = false;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isDependent = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup", cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="dependencies", referencedColumnName="id")
     */
    private ?DataModelDependencyGroup $dependencies = null;

    public function __construct(string $title, int $order, bool $isRepeated, bool $isDependent, DataModelVersion $dataModel)
    {
        $this->title = $title;
        $this->order = $order;
        $this->dataModel = $dataModel;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;

        $this->triples = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModelVersion $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    /**
     * @return Collection<string, Triple>
     */
    public function getTriples(): Collection
    {
        return $this->triples;
    }

    /**
     * @param Collection<string, Triple> $triples
     */
    public function setTriples(Collection $triples): void
    {
        $this->triples = $triples;
    }

    public function addTriple(Triple $triple): void
    {
        $this->triples->add($triple);
    }

    public function removeTriple(Triple $triple): void
    {
        $this->triples->removeElement($triple);
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function setIsRepeated(bool $isRepeated): void
    {
        $this->isRepeated = $isRepeated;
    }

    public function isDependent(): bool
    {
        return $this->isDependent;
    }

    public function setIsDependent(bool $isDependent): void
    {
        $this->isDependent = $isDependent;
    }

    public function getDependencies(): ?DataModelDependencyGroup
    {
        return $this->dependencies;
    }

    public function setDependencies(?DataModelDependencyGroup $dependencies): void
    {
        $this->dependencies = $dependencies;
    }
}
