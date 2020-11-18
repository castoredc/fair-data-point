<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\DataModelGroup;

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
