<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\DataModelMappingType;

class GetDataModelMappingCommand
{
    public function __construct(private RDFDistribution $distribution, private DataModelVersion $dataModelVersion, private DataModelMappingType $type)
    {
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }

    public function getType(): DataModelMappingType
    {
        return $this->type;
    }
}
