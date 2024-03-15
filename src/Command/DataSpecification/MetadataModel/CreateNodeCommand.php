<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateNodeCommand as CommonCreateNodeCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;

class CreateNodeCommand extends CommonCreateNodeCommand
{
    private MetadataModelVersion $metadataModelVersion;

    private ?MetadataFieldType $fieldType = null;

    public function __construct(MetadataModelVersion $metadataModelVersion, NodeType $type, string $title, ?string $description, string $value, ?XsdDataType $dataType, ?MetadataFieldType $fieldType)
    {
        parent::__construct($type, $title, $description, $value, $dataType);

        $this->metadataModelVersion = $metadataModelVersion;
        $this->fieldType = $fieldType;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function getFieldType(): ?MetadataFieldType
    {
        return $this->fieldType;
    }
}
