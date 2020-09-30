<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModelMapping\DataModelModuleMapping;
use App\Entity\Data\DataModelMapping\DataModelNodeMapping;
use App\Entity\Data\DistributionContents;
use App\Entity\FAIRData\AccessibleEntity;
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

    public function getMappingByModuleForCurrentVersion(DataModelModule $module): ?DataModelModuleMapping
    {
        return $this->getStudy()->getMappingByModuleAndVersion($module, $this->currentDataModelVersion);
    }

    public function getMappingByNodeForCurrentVersion(ValueNode $node): ?DataModelNodeMapping
    {
        return $this->getStudy()->getMappingByNodeAndVersion($node, $this->currentDataModelVersion);
    }
}
