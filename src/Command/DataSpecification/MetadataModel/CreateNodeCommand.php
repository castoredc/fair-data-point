<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateNodeCommand as CommonCreateNodeCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;

class CreateNodeCommand extends CommonCreateNodeCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion, NodeType $type, string $title, ?string $description, string $value, ?XsdDataType $dataType, private ?ResourceType $useAsTitle)
    {
        parent::__construct($type, $title, $description, $value, $dataType);
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function getUseAsTitle(): ?ResourceType
    {
        return $this->useAsTitle;
    }
}
