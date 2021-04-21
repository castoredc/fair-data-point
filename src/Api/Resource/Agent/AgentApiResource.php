<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Agent;

abstract class AgentApiResource implements ApiResource
{
    protected Agent $agent;

    /** @var array<string, int>|null */
    protected ?array $metadataCount = null;

    /**
     * @param array<string, int> $metadataCount
     */
    public function setMetadataCount(array $metadataCount): void
    {
        $this->metadataCount = $metadataCount;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->agent->getId(),
            'slug' => $this->agent->getSlug(),
            'name' => $this->agent->getName(),
        ];

        if ($this->metadataCount !== null) {
            $data['count'] = $this->metadataCount;
        }

        return $data;
    }
}
