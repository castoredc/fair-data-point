<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\Visualization;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;

class VisualizationNodeApiResource implements ApiResource
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
        return [
            'id' => $this->node->getId(),
            'label' => $this->node->getTitle(),
            'type' => $this->node->getType()->toString(),
        ];
    }
}
