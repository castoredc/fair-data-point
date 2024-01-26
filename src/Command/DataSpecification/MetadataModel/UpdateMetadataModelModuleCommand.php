<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;

class UpdateMetadataModelModuleCommand extends UpdateModelModuleCommand
{
    private MetadataModelGroup $module;

    public function __construct(MetadataModelGroup $module, string $title, int $order)
    {
        parent::__construct($title, $order);

        $this->module = $module;
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }
}
