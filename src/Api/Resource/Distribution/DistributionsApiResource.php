<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Distribution;
use App\Service\UriHelper;

class DistributionsApiResource implements ApiResource
{
    /** @var Distribution[] */
    private $distributions;

    /** @var UriHelper */
    private $uriHelper;

    /**
     * @param Distribution[] $distributions
     */
    public function __construct(array $distributions, UriHelper $uriHelper)
    {
        $this->distributions = $distributions;
        $this->uriHelper = $uriHelper;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->distributions as $distributions) {
            $data[] = (new DistributionApiResource($distributions, $this->uriHelper))->toArray();
        }

        return $data;
    }
}
