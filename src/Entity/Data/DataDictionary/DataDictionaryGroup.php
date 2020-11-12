<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryGroup
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
     * @ORM\ManyToOne(targetEntity="DataDictionaryVersion", inversedBy="groups", cascade={"persist"})
     * @ORM\JoinColumn(name="data_dictionary_version", referencedColumnName="id", nullable=false)
     */
    private DataDictionaryVersion $dataDictionaryVersion;

    /**
     * @ORM\OneToMany(targetEntity="Variable", mappedBy="group", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<Variable>
     */
    private Collection $variables;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isRepeated = false;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isDependent = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup", cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="dependencies", referencedColumnName="id")
     */
    private ?DataModelDependencyGroup $dependencies = null;

    public function __construct(string $title, int $order, bool $isRepeated, bool $isDependent, DataDictionaryVersion $dataDictionaryVersion)
    {
        $this->title = $title;
        $this->order = $order;
        $this->dataDictionaryVersion = $dataDictionaryVersion;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;

        $this->variables = new ArrayCollection();
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

    public function getDataDictionaryVersion(): DataDictionaryVersion
    {
        return $this->dataDictionaryVersion;
    }

    public function setDataDictionaryVersion(DataDictionaryVersion $dataDictionaryVersion): void
    {
        $this->dataDictionaryVersion = $dataDictionaryVersion;
    }

    /**
     * @return Collection<Variable>
     */
    public function getVariables(): Collection
    {
        return $this->variables;
    }

    /**
     * @param Collection<Variable> $variables
     */
    public function setVariables(Collection $variables): void
    {
        $this->variables = $variables;
    }

    public function addVariable(Variable $variable): void
    {
        $variable->setGroup($this);
        $this->variables->add($variable);
    }

    public function removeVariable(Variable $variable): void
    {
        $this->variables->removeElement($variable);
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
