<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;

class UpdateDataModelModuleCommand
{
    private DataModelModule $module;

    private string $title;

    private int $order;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DataModelDependencyGroup $dependencies = null;

    public function __construct(DataModelModule $module, string $title, int $order, bool $isRepeated, bool $isDependent, ?DataModelDependencyGroup $dependencies)
    {
        $this->module = $module;
        $this->title = $title;
        $this->order = $order;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getModule(): DataModelModule
    {
        return $this->module;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function isDependent(): bool
    {
        return $this->isDependent;
    }

    public function getDependencies(): ?DataModelDependencyGroup
    {
        return $this->dependencies;
    }
}
