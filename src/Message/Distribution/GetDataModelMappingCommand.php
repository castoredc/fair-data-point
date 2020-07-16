<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\RDF\RDFDistribution;

class GetDataModelMappingCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var DataModelVersion */
    private $dataModelVersion;

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
