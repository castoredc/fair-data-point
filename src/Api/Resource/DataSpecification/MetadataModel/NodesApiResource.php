<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\NodeType;
use function assert;

class NodesApiResource implements ApiResource
{
    private MetadataModelVersion $metadataModel;

    private ?NodeType $type;

    public function __construct(MetadataModelVersion $metadataModel, ?NodeType $type = null)
    {
        $this->metadataModel = $metadataModel;
        $this->type = $type;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'external' => [],
            'literal' => [],
            'record' => [],
            'value' => [],
        ];

        foreach ($this->metadataModel->getElements() as $node) {
            assert($node instanceof Node);
            $data[$node->getType()->toString()][] = (new NodeApiResource($node))->toArray();
        }

        if ($this->type !== null) {
            return $data[$this->type->toString()];
        }

        return $data;
    }
}
