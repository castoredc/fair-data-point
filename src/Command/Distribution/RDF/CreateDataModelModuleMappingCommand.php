<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\StructureType;

class CreateDataModelModuleMappingCommand extends CreateDataModelMappingCommand
{
    public function __construct(RDFDistribution $distribution, private string $module, private string $element, private StructureType $structureType, DataModelVersion $dataModelVersion)
    {
        parent::__construct($distribution, $dataModelVersion);
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
