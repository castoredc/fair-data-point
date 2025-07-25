<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Common\IriApiResource;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;

class NodeApiResource implements ApiResource
{
    public function __construct(private Node $node)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $value = $this->node->getValue() ?? null;

        if ($this->node instanceof ExternalIriNode) {
            $value = (new IriApiResource($this->node->getDataModelVersion(), $this->node->getIri()))->toArray();
        } elseif ($this->node instanceof LiteralNode || $this->node instanceof ValueNode) {
            $value = [
                'dataType' => $this->node->getDataType()?->toString(),
                'value' => $this->node->getValue(),
            ];
        }

        $repeated = false;

        if ($this->node instanceof ValueNode || $this->node instanceof InternalIriNode) {
            $repeated = $this->node->isRepeated();
        }

        return [
            'id' => $this->node->getId(),
            'type' => $this->node->getType()->toString(),
            'title' => $this->node->getTitle(),
            'description' => $this->node->getDescription(),
            'value' => $value,
            'repeated' => $repeated,
        ];
    }
}
