<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Version as VersionNumber;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification_version")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model" = "App\Entity\Data\DataModel\DataModelVersion",
 *     "dictionary" = "App\Entity\Data\DataDictionary\DataDictionaryVersion",
 * })
 */
abstract class Version
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataSpecification", inversedBy="versions",cascade={"persist"})
     * @ORM\JoinColumn(name="data_specification", referencedColumnName="id", nullable=false)
     */
    protected DataSpecification $dataSpecification;


    /** @ORM\Column(type="version") */
    private VersionNumber $version;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DistributionContents\DistributionContents", mappedBy="currentDataSpecificationVersion")
     *
     * @var Collection<DistributionContents>
     */
    private Collection $distributionContents;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="version", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"order" = "ASC", "id" = "ASC"})
     *
     * @var Collection<Group>
     */
    private Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity="Element", mappedBy="version", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<Element>
     */
    protected Collection $elements;

    public function __construct(VersionNumber $version)
    {
        $this->version = $version;
        $this->groups = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->distributionContents = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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
}
