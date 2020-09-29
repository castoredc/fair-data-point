<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DistributionContents;
use App\Entity\FAIRData\AccessibleEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents implements AccessibleEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModel", inversedBy="distributions")
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModel $dataModel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModelVersion", inversedBy="distributions")
     * @ORM\JoinColumn(name="data_model_version", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $currentDataModelVersion;

    /**
     * @ORM\OneToMany(targetEntity="DataModelMapping", mappedBy="distribution", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection<DataModelMapping>
     */
    private Collection $mappings;

    /** @ORM\Column(type="boolean") */
    private bool $isCached = false;

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/rdf';
    }

    /**
     * @return Collection<DataModelMapping>
     */
    public function getMappings(): Collection
    {
        return $this->mappings;
    }

    /**
     * @return Collection<DataModelNodeMapping>
     */
    public function getNodeMappings(): Collection
    {
        $return = new ArrayCollection();

        foreach ($this->mappings as $mapping) {
            if (! $mapping instanceof DataModelNodeMapping) {
                continue;
            }

            $return->add($mapping);
        }

        return $return;
    }

    /**
     * @return Collection<DataModelModuleMapping>
     */
    public function getModuleMappings(): Collection
    {
        $return = new ArrayCollection();

        foreach ($this->mappings as $mapping) {
            if (! $mapping instanceof DataModelModuleMapping) {
                continue;
            }

            $return->add($mapping);
        }

        return $return;
    }

    public function getMappingByNodeAndVersion(ValueNode $node, DataModelVersion $dataModelVersion): ?DataModelNodeMapping
    {
        foreach ($this->getNodeMappings() as $mapping) {
            if ($mapping->getNode() === $node && $mapping->getDataModelVersion() === $dataModelVersion) {
                return $mapping;
            }
        }

        return null;
    }

    public function getMappingByModuleAndVersion(DataModelModule $module, DataModelVersion $dataModelVersion): ?DataModelModuleMapping
    {
        foreach ($this->getModuleMappings() as $mapping) {
            if ($mapping->getModule() === $module && $mapping->getDataModelVersion() === $dataModelVersion) {
                return $mapping;
            }
        }

        return null;
    }

    public function getMappingByModuleForCurrentVersion(DataModelModule $module): ?DataModelModuleMapping
    {
        return $this->getMappingByModuleAndVersion($module, $this->currentDataModelVersion);
    }

    public function getMappingByNodeForCurrentVersion(ValueNode $node): ?DataModelNodeMapping
    {
        return $this->getMappingByNodeAndVersion($node, $this->currentDataModelVersion);
    }

    public function isCached(): bool
    {
        return $this->isCached;
    }

    public function setIsCached(bool $isCached): void
    {
        $this->isCached = $isCached;
    }

    public function getCurrentDataModelVersion(): DataModelVersion
    {
        return $this->currentDataModelVersion;
    }

    public function setCurrentDataModelVersion(DataModelVersion $dataModelVersion): void
    {
        if ($dataModelVersion->getDataModel() !== $this->dataModel) {
            return;
        }

        $this->currentDataModelVersion = $dataModelVersion;
    }
}
