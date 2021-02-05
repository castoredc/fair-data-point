<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Enum\StructureType;

class CreateDataModelModuleMappingCommand extends CreateDataModelMappingCommand
{
    private string $module;

    private string $element;

    private StructureType $structureType;

    public function __construct(RDFDistribution $distribution, string $module, string $element, StructureType $structureType, DataModelVersion $dataModelVersion)
    {
        parent::__construct($distribution, $dataModelVersion);

        $this->module = $module;
        $this->element = $element;
        $this->structureType = $structureType;
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
}
