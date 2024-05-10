<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Visualization;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\Node;

class VisualizationNodeApiResource implements ApiResource
{
    public function __construct(private Node $node)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->node->getId(),
            'label' => $this->node->getTitle(),
            'type' => $this->node->getType()->toString(),
        ];
    }
}
