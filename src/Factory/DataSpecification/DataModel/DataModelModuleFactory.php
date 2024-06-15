<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Factory\DataSpecification\Common\Dependency\DependencyGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;

class DataModelModuleFactory
{
    public function __construct(private DependencyGroupFactory $dependencyGroupFactory)
    {
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
