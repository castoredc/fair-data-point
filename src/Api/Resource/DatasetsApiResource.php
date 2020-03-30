<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Dataset;

class DatasetsApiResource implements ApiResource
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
            $data[] = (new DatasetApiResource($dataset))->toArray();
        }

        return $data;
    }
}
