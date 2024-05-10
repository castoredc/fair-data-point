<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\NodeType;
use function assert;

class NodesApiResource implements ApiResource
{
    public function __construct(private DataModelVersion $dataModel, private ?NodeType $type = null)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'external' => [],
            'internal' => [],
            'literal' => [],
            'record' => [],
            'value' => [],
        ];

        foreach ($this->dataModel->getElements() as $node) {
            assert($node instanceof Node);
            $data[$node->getType()->toString()][] = (new NodeApiResource($node))->toArray();
        }

        if ($this->type !== null) {
            return $data[$this->type->toString()];
        }

        return $data;
    }
}
