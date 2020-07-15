<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\RDF\RDFDistribution;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model")
 * @ORM\HasLifecycleCallbacks
 */
class DataModel
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="DataModelVersion", mappedBy="dataModel", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<DataModelVersion>
     */
    private $versions;

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
     * @return Collection<DataModelVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(DataModelVersion $version): void
    {
        $version->setDataModel($this);
        $this->versions->add($version);
    }


    public function getLatestVersion(): DataModelVersion
    {
        return $this->versions->last();
    }

    /**
     * @return Collection<RDFDistribution>
     */
    public function getDistributions(): Collection
    {
        $return = [];

        foreach($this->versions as $version) {
            /** @var DataModelVersion $version */

            foreach($version->getDistributions() as $distribution) {
                /** @var RDFDistribution $distribution */
                $return[$distribution->getId()] = $distribution;
            }
        }

        return array_values($return);
    }
}
