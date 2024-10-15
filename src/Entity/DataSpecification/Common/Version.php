<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Version as VersionNumber;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification_version')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['model' => 'App\Entity\DataSpecification\DataModel\DataModelVersion', 'metadata_model' => 'App\Entity\DataSpecification\MetadataModel\MetadataModelVersion', 'dictionary' => 'App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion'])]
abstract class Version
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'data_specification', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: DataSpecification::class, inversedBy: 'versions', cascade: ['persist'])]
    protected DataSpecification $dataSpecification;

    #[ORM\Column(type: 'version')]
    private VersionNumber $version;

    /** @var Collection<DistributionContents> */
    #[ORM\OneToMany(targetEntity: DistributionContents::class, mappedBy: 'currentDataSpecificationVersion')]
    private Collection $distributionContents;

    /** @var Collection<Group> */
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'version', cascade: ['persist'])]
    #[ORM\OrderBy(['order' => 'ASC', 'id' => 'ASC'])]
    private Collection $groups;

    /** @var Collection<Element> */
    #[ORM\OneToMany(targetEntity: Element::class, mappedBy: 'version', cascade: ['persist'])]
    #[ORM\OrderBy(['title' => 'ASC'])]
    protected Collection $elements;

    /** @var Collection<OptionGroup> */
    #[ORM\OneToMany(targetEntity: OptionGroup::class, mappedBy: 'version', cascade: ['persist'])]
    #[ORM\OrderBy(['title' => 'ASC'])]
    protected Collection $optionGroups;

    public function __construct(VersionNumber $version)
    {
        $this->version = $version;
        $this->groups = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->optionGroups = new ArrayCollection();
        $this->distributionContents = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getVersion(): VersionNumber
    {
        return $this->version;
    }

    public function setVersion(VersionNumber $version): void
    {
        $this->version = $version;
    }

    /** @return Collection<Group> */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /** @return Collection<Group> */
    public function getRepeatedGroups(): Collection
    {
        $return = new ArrayCollection();

        foreach ($this->groups as $group) {
            if (! $group->isRepeated()) {
                continue;
            }

            $return->add($group);
        }

        return $return;
    }

    /** @param Collection<Group> $groups */
    public function setGroups(Collection $groups): void
    {
        $this->groups = $groups;
    }

    public function addGroup(Group $group): void
    {
        $newGroupOrder = $group->getOrder();
        $newGroups = new ArrayCollection();

        $order = 1;
        foreach ($this->groups as $currentGroup) {
            /** @var Group $currentGroup */
            $newOrder = $order >= $newGroupOrder ? $order + 1 : $order;
            $currentGroup->setOrder($newOrder);
            $newGroups->add($currentGroup);

            $order++;
        }

        $newGroups->add($group);
        $this->groups = $newGroups;
    }

    public function reorderGroups(): void
    {
        $newGroups = new ArrayCollection();
        $order = 1;

        foreach ($this->groups as $currentGroup) {
            /** @var Group $currentGroup */
            $currentGroup->setOrder($order);
            $newGroups->add($currentGroup);

            $order++;
        }

        $this->groups = $newGroups;
    }

    public function removeGroup(Group $group): void
    {
        $this->groups->removeElement($group);

        $this->reorderGroups();
    }

    /** @return Collection<DistributionContents> */
    public function getDistributionContents(): Collection
    {
        return $this->distributionContents;
    }

    public function getDataSpecification(): DataSpecification
    {
        return $this->dataSpecification;
    }

    public function setDataSpecification(DataSpecification $dataSpecification): void
    {
        $this->dataSpecification = $dataSpecification;
    }

    /** @return Collection<Element> */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): void
    {
        $element->setVersion($this);
        $this->elements->add($element);
    }

    public function removeElement(Element $element): void
    {
        $this->elements->removeElement($element);
    }

    /** @return Collection<OptionGroup> */
    public function getOptionGroups(): Collection
    {
        return $this->optionGroups;
    }

    public function addOptionGroup(OptionGroup $optionGroup): void
    {
        $optionGroup->setVersion($this);
        $this->optionGroups->add($optionGroup);
    }

    public function removeOptionGroup(OptionGroup $optionGroup): void
    {
        $this->optionGroups->removeElement($optionGroup);
    }
}
