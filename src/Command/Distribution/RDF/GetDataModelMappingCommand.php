<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Enum\DataModelMappingType;

class GetDataModelMappingCommand
{
    private RDFDistribution $distribution;

    private DataModelVersion $dataModelVersion;

    private DataModelMappingType $type;

    public function __construct(RDFDistribution $distribution, DataModelVersion $dataModelVersion, DataModelMappingType $type)
    {
        $this->distribution = $distribution;
        $this->dataModelVersion = $dataModelVersion;
        $this->type = $type;
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
