<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\StudyMetadataFilterApiResource;
use App\Entity\FAIRData\Dataset;

class DatasetsFilterApiResource implements ApiResource
{
    /** @var Dataset[] */
    private array $datasets;

    /** @param Dataset[] $datasets */
    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $metadata = [];

        foreach ($this->datasets as $dataset) {
            $study = $dataset->getStudy();
            if (! $study->hasMetadata()) {
                continue;
            }

            $metadata[] = $study->getLatestMetadata();
        }

        return (new StudyMetadataFilterApiResource($metadata))->toArray();
    }
}
