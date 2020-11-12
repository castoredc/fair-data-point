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
 * @ORM\Table(name="data_dictionary")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionary
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

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $description = null;

    /**
     * @ORM\OneToMany(targetEntity="DataDictionaryVersion", mappedBy="dataDictionary", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<DataDictionaryVersion>
     */
    private Collection $versions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DistributionContents\CSVDistribution", mappedBy="dataDictionary")
     *
     * @var Collection<CSVDistribution>
     */
    private Collection $distributions;

    public function __construct(string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;

        $this->versions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<DataDictionaryVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(DataDictionaryVersion $version): void
    {
        $version->setDataDictionary($this);
        $this->versions->add($version);
    }

    public function getLatestVersion(): DataDictionaryVersion
    {
        return $this->versions->last();
    }

    public function hasVersion(Version $version): bool
    {
        foreach ($this->versions as $dataModelVersion) {
            if ($dataModelVersion->getVersion() === $version) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<CSVDistribution>
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }
}
