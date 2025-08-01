<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Common\IriApiResource;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
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

        if ($this->node instanceof RecordNode) {
            $data['value'] = [
                'resourceType' => $this->node->getResourceType()->toString(),
            ];
        } elseif ($this->node instanceof InternalIriNode) {
            $data['repeated'] = $this->node->isRepeated();
        } elseif ($this->node instanceof ExternalIriNode) {
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
            ];
        } elseif ($this->node instanceof ChildrenNode) {
            $data['value'] = [
                'resourceType' => $this->node->getResourceType()->toString(),
            ];
        } elseif ($this->node instanceof ParentsNode) {
            $data['value'] = [
                'resourceType' => $this->node->getResourceType()->toString(),
            ];
        }

        return $data;
    }
}
