<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;

class NodesApiResource implements ApiResource
{
    /** @var DataModelVersion */
    private $dataModel;

    /** @var NodeType */
    private $type;

    public function __construct(DataModelVersion $dataModel, ?NodeType $type = null)
    {
        $this->dataModel = $dataModel;
        $this->type = $type;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'external' => [],
            'internal' => [],
            'literal' => [],
            'record' => [],
            'value' => [],
        ];

        foreach ($this->dataModel->getNodes() as $node) {
            $data[$node->getType()->toString()][] = (new NodeApiResource($node))->toArray();
        }

        if ($this->type !== null) {
            return $data[$this->type->toString()];
        }

        return $data;
    }
}
