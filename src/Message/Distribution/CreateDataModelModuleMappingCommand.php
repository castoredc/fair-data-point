<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\StructureType;

class CreateDataModelModuleMappingCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var string */
    private $module;

    /** @var string */
    private $element;

    /** @var StructureType */
    private $structureType;

    /** @var DataModelVersion */
    private $dataModelVersion;

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
