<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Factory\DataSpecification\Common\Dependency\DependencyGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;

class DataDictionaryGroupFactory
{
    private DependencyGroupFactory $dependencyGroupFactory;

    public function __construct(DependencyGroupFactory $dependencyGroupFactory)
    {
        $this->dependencyGroupFactory = $dependencyGroupFactory;
    }

    /** @param array<mixed> $data */
    public function createFromJson(DataDictionaryVersion $version, ArrayCollection $nodes, array $data): DataDictionaryGroup
    {
        $newGroup = new DataDictionaryGroup(
            $data['title'],
            $data['order'],
            $data['repeated'],
            $data['dependent'],
            $version
        );

        if ($newGroup->isDependent() && $data['dependencies'] !== null) {
            $newGroup->setDependencies($this->dependencyGroupFactory->createFromJson($data['dependencies'], $nodes));
        }

        return $newGroup;
    }
}
