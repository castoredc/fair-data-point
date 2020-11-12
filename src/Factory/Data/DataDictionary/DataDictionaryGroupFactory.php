<?php
declare(strict_types=1);

namespace App\Factory\Data\DataDictionary;

use App\Entity\Data\DataDictionary\DataDictionaryGroup;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use Doctrine\Common\Collections\ArrayCollection;

class DataDictionaryGroupFactory
{
    private DataDictionaryDependencyGroupFactory $dataDictionaryDependencyGroupFactory;

    public function __construct(DataDictionaryDependencyGroupFactory $dataDictionaryDependencyGroupFactory)
    {
        $this->dataDictionaryDependencyGroupFactory = $dataDictionaryDependencyGroupFactory;
    }

    /**
     * @param array<mixed> $data
     */
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
            $newGroup->setDependencies($this->dataDictionaryDependencyGroupFactory->createFromJson($data['dependencies'], $nodes));
        }

        return $newGroup;
    }
}
