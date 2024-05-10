<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataDictionary;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use function sprintf;

class DataDictionaryGroupApiResource implements ApiResource
{
    public function __construct(private DataDictionaryGroup $group)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        // TODO: Add variables
        return [
            'id' => $this->group->getId(),
            'title' => $this->group->getTitle(),
            'displayName' => sprintf('Group %d. %s', $this->group->getOrder(), $this->group->getTitle()),
            'order' => $this->group->getOrder(),
            'repeated' => $this->group->isRepeated(),
            'dependent' => $this->group->isDependent(),
            'dependencies' => $this->group->getDependencies() !== null ? (new DataDictionaryDependencyApiResource($this->group->getDependencies()))->toArray() : null,
            'variables' => [],
        ];
    }
}
