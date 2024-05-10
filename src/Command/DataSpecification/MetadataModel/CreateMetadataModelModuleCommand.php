<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\ResourceType;

class CreateMetadataModelModuleCommand extends CreateModelModuleCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion, string $title, int $order, private ResourceType $resourceType)
    {
        parent::__construct($title, $order);
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }
}
