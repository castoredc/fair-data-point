<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

abstract class CreateDataModelMappingCommand
{
    protected RDFDistribution $distribution;

    protected DataModelVersion $dataModelVersion;

    public function __construct(RDFDistribution $distribution, DataModelVersion $dataModelVersion)
    {
        $this->distribution = $distribution;
        $this->dataModelVersion = $dataModelVersion;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
