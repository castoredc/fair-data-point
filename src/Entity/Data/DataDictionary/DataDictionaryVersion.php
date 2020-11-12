<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_version")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryVersion
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\OneToMany(targetEntity="DataDictionaryGroup", mappedBy="dataDictionaryVersion", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"order" = "ASC", "id" = "ASC"})
     *
     * @var Collection<DataDictionaryGroup>
     */
    private Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DistributionContents\CSVDistribution", mappedBy="currentDataDictionaryVersion")
     *
     * @var Collection<CSVDistribution>
     */
    private Collection $distributions;

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionary", inversedBy="versions",cascade={"persist"})
     * @ORM\JoinColumn(name="data_dictionary", referencedColumnName="id", nullable=false)
     */
    private DataDictionary $dataDictionary;

    /** @ORM\Column(type="version") */
    private Version $version;

    public function __construct(Version $version)
    {
        $this->version = $version;

        $this->groups = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Collection<DataDictionaryGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @return Collection<DataDictionaryGroup>
     */
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

    /**
     * @param Collection<DataDictionaryGroup> $groups
     */
    public function setGroups(Collection $groups): void
    {
        $this->groups = $groups;
    }

    public function addGroup(DataDictionaryGroup $group): void
    {
        $newGroupOrder = $group->getOrder();
        $newGroups = new ArrayCollection();

        $order = 1;
        foreach ($this->groups as $currentGroup) {
            /** @var DataDictionaryGroup $currentGroup */
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
            /** @var DataDictionaryGroup $currentGroup */
            $currentGroup->setOrder($order);
            $newGroups->add($currentGroup);

            $order++;
        }

        $this->groups = $newGroups;
    }

    public function removeGroup(DataDictionaryGroup $group): void
    {
        $this->groups->removeElement($group);

        $this->reorderGroups();
    }

    public function getDataDictionary(): DataDictionary
    {
        return $this->dataDictionary;
    }

    public function setDataDictionary(DataDictionary $dataDictionary): void
    {
        $this->dataDictionary = $dataDictionary;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    /**
     * @return Collection<CSVDistribution>
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }
}
