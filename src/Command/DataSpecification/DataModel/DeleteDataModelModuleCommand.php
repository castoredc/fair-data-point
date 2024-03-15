<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelGroup;

class DeleteDataModelModuleCommand
{
    private DataModelGroup $module;

    public function __construct(DataModelGroup $module)
    {
        $this->module = $module;
    }

    public function getModule(): DataModelGroup
    {
        return $this->module;
    }
}
