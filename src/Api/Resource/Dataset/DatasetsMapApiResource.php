<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;

class DatasetsMapApiResource implements ApiResource
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
        $data = [];

        foreach ($this->datasets as $dataset) {
            foreach ($dataset->getStudy()->getLatestMetadata()->getOrganizations() as $organization) {
                if (! $organization->hasCoordinates()) {
                    continue;
                }

                $data[] = (new DatasetMapApiResource($dataset, $organization))->toArray();
            }
        }

        return $data;
    }
}
