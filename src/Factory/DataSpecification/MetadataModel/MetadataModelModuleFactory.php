<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Factory\DataSpecification\Common\Dependency\DependencyGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;

class MetadataModelModuleFactory
{
    private DependencyGroupFactory $dependencyGroupFactory;

    public function __construct(DependencyGroupFactory $dependencyGroupFactory)
    {
        $this->dependencyGroupFactory = $dependencyGroupFactory;
    }

    /** @param array<mixed> $data */
    public function createFromJson(MetadataModelVersion $version, ArrayCollection $nodes, array $data): MetadataModelGroup
    {
        $newModule = new MetadataModelGroup(
            $data['title'],
            $data['order'],
            $version
        );

        if ($newModule->isDependent() && $data['dependencies'] !== null) {
            $newModule->setDependencies($this->dependencyGroupFactory->createFromJson($data['dependencies'], $nodes));
        }

        return $newModule;
    }
}
