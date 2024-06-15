<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateModelModuleCommand;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class CreateDataModelModuleCommand extends CreateModelModuleCommand
{
    public function __construct(private DataModelVersion $dataModelVersion, string $title, int $order, private bool $isRepeated, private bool $isDependent, private ?DependencyGroup $dependencies = null)
    {
        parent::__construct($title, $order);
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
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
