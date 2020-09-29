<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\StudyMetadataFilterApiResource;
use App\Entity\Study;

class StudiesFilterApiResource implements ApiResource
{
    /** @var Study[] */
    private array $studies;

    /**
     * @param Study[] $studies
     */
    public function __construct(array $studies)
    {
        $this->studies = $studies;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $metadata = [];

        foreach ($this->studies as $study) {
            if (! $study->hasMetadata()) {
                continue;
            }

            $metadata[] = $study->getLatestMetadata();
        }

        return (new StudyMetadataFilterApiResource($metadata))->toArray();
    }
}
