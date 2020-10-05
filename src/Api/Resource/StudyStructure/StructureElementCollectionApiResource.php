<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\StructureCollection\StructureElementCollection;

class StructureElementCollectionApiResource implements ApiResource
{
    private StructureElementCollection $collection;

    public function __construct(StructureElementCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        if ($this->collection->getElements() === null) {
            return [];
        }

        $data = [];

        foreach ($this->collection->getElements() as $element) {
            $data[] = (new StructureElementApiResource($element))->toArray();
        }

        return $data;
    }
}
