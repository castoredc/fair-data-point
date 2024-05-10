<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;

class UpdateMetadataModelModuleCommand extends UpdateModelModuleCommand
{
    public function __construct(private MetadataModelGroup $module, string $title, int $order)
    {
        parent::__construct($title, $order);
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }
}
