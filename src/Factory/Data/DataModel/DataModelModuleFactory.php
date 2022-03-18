<?php
declare(strict_types=1);

namespace App\Factory\Data\DataModel;

use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Factory\Data\DataSpecification\Dependency\DependencyGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;

class DataModelModuleFactory
{
    private DependencyGroupFactory $dependencyGroupFactory;

    public function __construct(DependencyGroupFactory $dependencyGroupFactory)
    {
        $this->dependencyGroupFactory = $dependencyGroupFactory;
    }

    /** @param array<mixed> $data */
    public function createFromJson(DataModelVersion $version, ArrayCollection $nodes, array $data): DataModelGroup
    {
        $newModule = new DataModelGroup(
            $data['title'],
            $data['order'],
            $data['repeated'],
            $data['dependent'],
            $version
        );

        if ($newModule->isDependent() && $data['dependencies'] !== null) {
            $newModule->setDependencies($this->dependencyGroupFactory->createFromJson($data['dependencies'], $nodes));
        }

        return $newModule;
    }
}
