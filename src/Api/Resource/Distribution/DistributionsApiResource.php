<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Distribution\Distribution;

class DistributionsApiResource implements ApiResource
{
    /** @var Distribution[] */
    private $distributions;

    /**
     * @param Distribution[] $distributions
     */
    public function __construct(array $distributions)
    {
        $this->distributions = $distributions;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->distributions as $distributions) {
            $data[] = (new DistributionApiResource($distributions, false))->toArray();
        }

        return $data;
    }
}
