<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelModuleCommand;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\DataModel\DataModelGroup;

class UpdateDataModelModuleCommand extends UpdateModelModuleCommand
{
    public function __construct(private DataModelGroup $module, string $title, int $order, private bool $isRepeated, private bool $isDependent, private ?DependencyGroup $dependencies = null)
    {
        parent::__construct($title, $order);
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
