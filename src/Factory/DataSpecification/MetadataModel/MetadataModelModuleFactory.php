<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Factory\DataSpecification\Common\Dependency\DependencyGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;

class MetadataModelModuleFactory
{
    public function __construct(private DependencyGroupFactory $dependencyGroupFactory)
    {
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
