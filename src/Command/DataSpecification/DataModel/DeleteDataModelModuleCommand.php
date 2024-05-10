<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelGroup;

class DeleteDataModelModuleCommand
{
    public function __construct(private DataModelGroup $module)
    {
    }

    public function getModule(): DataModelGroup
    {
        return $this->module;
    }
}
