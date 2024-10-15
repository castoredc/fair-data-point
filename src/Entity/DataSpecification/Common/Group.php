<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification_group')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['model' => 'App\Entity\DataSpecification\DataModel\DataModelGroup', 'metadata_model' => 'App\Entity\DataSpecification\MetadataModel\MetadataModelGroup', 'dictionary' => 'App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup'])]
abstract class Group
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'version', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Version::class, inversedBy: 'groups', cascade: ['persist'])]
    private Version $version;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    private ?int $order;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isRepeated = false;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isDependent = false;

    /**
     * @var Collection<Element>
     */
    #[ORM\OneToMany(targetEntity: \Element::class, mappedBy: 'group', cascade: ['persist'])]
    private Collection $elements;

    /**
     * @var Collection<ElementGroup>
     */
    #[ORM\OneToMany(targetEntity: \ElementGroup::class, mappedBy: 'group', cascade: ['persist'])]
    private Collection $elementGroups;

    #[ORM\JoinColumn(name: 'dependencies', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: \App\Entity\DataSpecification\Common\Dependency\DependencyGroup::class, cascade: ['persist'], fetch: 'EAGER')]
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
        return (string) $this->id;
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
