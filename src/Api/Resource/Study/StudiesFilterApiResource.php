<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\StudyMetadataFilterApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Entity\Castor\Study;

class StudiesFilterApiResource extends PaginatedApiResource
{
    /** @var Study[] */
    private $studies;

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
            if($study->hasMetadata()) {
                $metadata[] = $study->getLatestMetadata();
            }
        }

        return (new StudyMetadataFilterApiResource($metadata))->toArray();
    }
}
