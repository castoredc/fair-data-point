<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelModuleCommand;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\DataModel\DataModelGroup;

class UpdateDataModelModuleCommand extends UpdateModelModuleCommand
{
    private DataModelGroup $module;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DependencyGroup $dependencies = null;

    public function __construct(DataModelGroup $module, string $title, int $order, bool $isRepeated, bool $isDependent, ?DependencyGroup $dependencies)
    {
        parent::__construct($title, $order);

        $this->module = $module;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getModule(): DataModelGroup
    {
        return $this->module;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function isDependent(): bool
    {
        return $this->isDependent;
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }
}
