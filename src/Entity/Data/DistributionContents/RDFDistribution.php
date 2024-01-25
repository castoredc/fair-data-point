<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents;

use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\RDFDistributionDatabaseType;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Distribution;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents implements AccessibleEntity
{
    /** @ORM\Column(type="RDFDistributionDatabaseType") */
    private RDFDistributionDatabaseType $databaseType;

    public function __construct(Distribution $distribution)
    {
        parent::__construct($distribution);

        $this->databaseType = RDFDistributionDatabaseType::mysql();
    }

    public function getDataModel(): DataModel
    {
        assert($this->dataSpecification instanceof DataModel);

        return $this->dataSpecification;
    }

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/rdf';
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->setDataSpecification($dataModel);
    }

    public function setCurrentDataModelVersion(DataModelVersion $dataModelVersion): void
    {
        $this->setCurrentDataSpecificationVersion($dataModelVersion);
    }

    public function getCurrentDataModelVersion(): DataModelVersion
    {
        assert($this->currentDataSpecificationVersion instanceof DataModelVersion);

        return $this->currentDataSpecificationVersion;
    }

    public function getSparqlUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/sparql';
    }

    public function getType(): string
    {
        return 'rdf';
    }

    public function getDatabaseType(): RDFDistributionDatabaseType
    {
        return $this->databaseType;
    }

    public function setDatabaseType(RDFDistributionDatabaseType $databaseType): void
    {
        $this->databaseType = $databaseType;
    }
}
