<?php
declare(strict_types=1);

namespace App\Factory\Data\DataModel;

use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use Doctrine\Common\Collections\ArrayCollection;

class DataModelModuleFactory
{
    /** @var DataModelDependencyGroupFactory */
    private $dataModelDependencyGroupFactory;

    public function __construct(DataModelDependencyGroupFactory $dataModelDependencyGroupFactory)
    {
        $this->dataModelDependencyGroupFactory = $dataModelDependencyGroupFactory;
    }

    /**
     * @param array<mixed> $data
     */
    public function createFromJson(DataModelVersion $version, ArrayCollection $nodes, array $data): DataModelModule
    {
        $newModule = new DataModelModule(
            $data['title'],
            $data['order'],
            $data['repeated'],
            $data['dependent'],
            $version
        );

        if ($newModule->isDependent() && $data['dependencies'] !== null) {
            $newModule->setDependencies($this->dataModelDependencyGroupFactory->createFromJson($data['dependencies'], $nodes));
        }

        return $newModule;
    }
}
