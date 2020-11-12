<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Enum\StructureType;

class CreateDataModelModuleMappingCommand
{
    private RDFDistribution $distribution;

    private string $module;

    private string $element;

    private StructureType $structureType;

    private DataModelVersion $dataModelVersion;

    public function __construct(RDFDistribution $distribution, string $module, string $element, StructureType $structureType, DataModelVersion $dataModelVersion)
    {
        $this->distribution = $distribution;
        $this->module = $module;
        $this->element = $element;
        $this->structureType = $structureType;
        $this->dataModelVersion = $dataModelVersion;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getStructureType(): StructureType
    {
        return $this->structureType;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
