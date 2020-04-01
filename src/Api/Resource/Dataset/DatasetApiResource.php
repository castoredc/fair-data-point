<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;

class DatasetApiResource implements ApiResource
{
    /** @var Dataset */
    private $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->dataset->toBasicArray();
    }
}
