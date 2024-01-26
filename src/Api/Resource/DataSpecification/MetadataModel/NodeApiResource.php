<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Common\IriApiResource;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;

class NodeApiResource implements ApiResource
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $value = $this->node->getValue() ?? null;

        if ($this->node instanceof ExternalIriNode) {
            $value = (new IriApiResource($this->node->getMetadataModelVersion(), $this->node->getIri()))->toArray();
        } elseif ($this->node instanceof LiteralNode || $this->node instanceof ValueNode) {
            $value = [
                'dataType' => $this->node->getDataType() !== null ? $this->node->getDataType()->toString() : null,
                'value' => $this->node->getValue(),
            ];
        }

        return [
            'id' => $this->node->getId(),
            'type' => $this->node->getType()->toString(),
            'title' => $this->node->getTitle(),
            'description' => $this->node->getDescription(),
            'value' => $value,
        ];
    }
}
