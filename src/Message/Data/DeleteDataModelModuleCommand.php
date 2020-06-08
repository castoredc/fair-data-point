<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelModule;

class DeleteDataModelModuleCommand
{
    /** @var DataModelModule */
    private $module;

    public function __construct(DataModelModule $module)
    {
        $this->module = $module;
    }

    public function getModule(): DataModelModule
    {
        return $this->module;
    }
}
