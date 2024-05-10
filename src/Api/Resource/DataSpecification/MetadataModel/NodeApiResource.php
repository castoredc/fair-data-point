<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Common\IriApiResource;
use App\Api\Resource\DataSpecification\Common\OptionGroupApiResource;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;

class NodeApiResource implements ApiResource
{
    public function __construct(private Node $node)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'id' => $this->node->getId(),
            'type' => $this->node->getType()->toString(),
            'title' => $this->node->getTitle(),
            'description' => $this->node->getDescription(),
            'value' => $this->node->getValue() ?? null,
        ];

        if ($this->node instanceof ExternalIriNode) {
            $data['value'] = (new IriApiResource($this->node->getMetadataModelVersion(), $this->node->getIri()))->toArray();
        } elseif ($this->node instanceof LiteralNode) {
            $data['value'] = [
                'dataType' => $this->node->getDataType()->toString(),
                'value' => $this->node->getValue(),
            ];
        } elseif ($this->node instanceof ValueNode) {
            $data['value'] = [
                'dataType' => $this->node->getDataType()?->toString(),
                'value' => $this->node->getValue(),
                'optionGroup' => $this->node->getOptionGroup() !== null ? (new OptionGroupApiResource($this->node->getOptionGroup()))->toArray() : null,
            ];
        }

        return $data;
    }
}
