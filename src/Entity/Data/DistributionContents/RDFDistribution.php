<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\FAIRData\AccessibleEntity;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents implements AccessibleEntity
{
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
}
