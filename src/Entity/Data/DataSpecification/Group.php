<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification_group")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model" = "App\Entity\Data\DataModel\DataModelGroup",
 *     "dictionary" = "App\Entity\Data\DataDictionary\DataDictionaryGroup",
 * })
 */
abstract class Group
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="groups", cascade={"persist"})
     * @ORM\JoinColumn(name="version", referencedColumnName="id", nullable=false)
     */
    private Version $version;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(name="orderNumber", type="integer", nullable=true) */
    private ?int $order;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isRepeated = false;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isDependent = false;

    /**
     * @ORM\OneToMany(targetEntity="Element", mappedBy="group", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<Element>
     */
    private Collection $elements;

    /**
     * @ORM\OneToMany(targetEntity="ElementGroup", mappedBy="group", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<ElementGroup>
     */
    private Collection $elementGroups;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Data\DataSpecification\Dependency\DependencyGroup", cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="dependencies", referencedColumnName="id")
     */
    protected ?DependencyGroup $dependencies = null;

    public function __construct(string $title, int $order, bool $isRepeated, bool $isDependent, Version $version)
    {
        $this->title = $title;
        $this->order = $order;
        $this->version = $version;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;

        $this->elements = new ArrayCollection();
        $this->elementGroups = new ArrayCollection();
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

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    /** @return Collection<Element> */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    /** @param Collection<Element> $elements */
    public function setElements(Collection $elements): void
    {
        $this->elements = $elements;
    }

    public function addElement(Element $element): void
    {
        $element->setGroup($this);
        $this->elements->add($element);
    }

    public function removeElement(Element $element): void
    {
        $this->elements->removeElement($element);
    }

    /** @return Collection<string, ElementGroup> */
    public function getElementGroups(): Collection
    {
        return $this->elementGroups;
    }

    /** @param Collection<string, ElementGroup> $elementGroups */
    public function setElementGroups(Collection $elementGroups): void
    {
        $this->elementGroups = $elementGroups;
    }

    public function addElementGroup(ElementGroup $elementGroup): void
    {
        $this->elementGroups->add($elementGroup);
    }

    public function removeElementGroup(ElementGroup $elementGroup): void
    {
        $this->elementGroups->removeElement($elementGroup);
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }

    public function setDependencies(?DependencyGroup $dependencies): void
    {
        $this->dependencies = $dependencies;
    }
}
