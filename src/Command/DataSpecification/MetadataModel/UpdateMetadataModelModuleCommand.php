<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\Enum\ResourceType;

class UpdateMetadataModelModuleCommand extends UpdateModelModuleCommand
{
    public function __construct(private MetadataModelGroup $module, string $title, int $order, private ResourceType $resourceType)
    {
        parent::__construct($title, $order);
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }
}
