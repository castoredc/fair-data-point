<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata;

class StudyMetadataFilterApiResource implements ApiResource
{
    /** @param StudyMetadata[] $metadata */
    public function __construct(private array $metadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return $this->generateFilters();
    }

    /** @return array<mixed> */
    private function generateFilters(): array
    {
        // TODO
        $metadata = $this->metadata;

        return [];
    }
}
