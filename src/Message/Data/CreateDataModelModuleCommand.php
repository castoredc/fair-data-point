<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;

class CreateDataModelModuleCommand
{
    /** @var DataModelVersion */
    private $dataModel;

    /** @var string */
    private $title;

    /** @var int */
    private $order;

    /** @var bool */
    private $isRepeated;

    /** @var bool */
    private $isDependent;

    /** @var DataModelDependencyGroup|null */
    private $dependencies;

    public function __construct(DataModelVersion $dataModel, string $title, int $order, bool $isRepeated, bool $isDependent, ?DataModelDependencyGroup $dependencies)
    {
        $this->dataModel = $dataModel;
        $this->title = $title;
        $this->order = $order;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
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
