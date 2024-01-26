<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;

class DeleteMetadataModelModuleCommand
{
    private MetadataModelGroup $module;

    public function __construct(MetadataModelGroup $module)
    {
        $this->module = $module;
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }
}
