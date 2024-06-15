<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;

class DeleteMetadataModelModuleCommand
{
    public function __construct(private MetadataModelGroup $module)
    {
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }
}
