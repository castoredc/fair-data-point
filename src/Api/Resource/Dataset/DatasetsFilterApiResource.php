<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\StudyMetadataFilterApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Dataset;

class DatasetsFilterApiResource extends PaginatedApiResource
{
    /** @var Dataset[] */
    private $datasets;

    /**
     * @param Dataset[] $datasets
     */
    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $metadata = [];

        foreach ($this->datasets as $dataset) {
            $study = $dataset->getStudy();
            if($study->hasMetadata()) {
                $metadata[] = $study->getLatestMetadata();
            }
        }

        return (new StudyMetadataFilterApiResource($metadata))->toArray();
    }
}
