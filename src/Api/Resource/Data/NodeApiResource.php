<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;

class NodeApiResource implements ApiResource
{
    /** @var Node */
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $value = $this->node->getValue() ?? null;

        if ($this->node instanceof ExternalIriNode) {
            $value = (new IriApiResource($this->node->getDataModel(), $this->node->getIri()))->toArray();
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
