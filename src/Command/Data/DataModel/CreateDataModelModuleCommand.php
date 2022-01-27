<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;

class CreateDataModelModuleCommand
{
    private DataModelVersion $dataModelVersion;

    private string $title;

    private int $order;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DependencyGroup $dependencies = null;

    public function __construct(DataModelVersion $dataModelVersion, string $title, int $order, bool $isRepeated, bool $isDependent, ?DependencyGroup $dependencies)
    {
        $this->dataModelVersion = $dataModelVersion;
        $this->title = $title;
        $this->order = $order;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
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

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }
}
