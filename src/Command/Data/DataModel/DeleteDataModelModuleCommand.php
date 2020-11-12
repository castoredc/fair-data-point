<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\DataModelModule;

class DeleteDataModelModuleCommand
{
    private DataModelModule $module;

    public function __construct(DataModelModule $module)
    {
        $this->module = $module;
    }

    public function getModule(): DataModelModule
    {
        return $this->module;
    }
}
