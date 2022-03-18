<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Enum\NodeType;
use function assert;

class NodesApiResource implements ApiResource
{
    private DataModelVersion $dataModel;

    private ?NodeType $type;

    public function __construct(DataModelVersion $dataModel, ?NodeType $type = null)
    {
        $this->dataModel = $dataModel;
        $this->type = $type;
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
