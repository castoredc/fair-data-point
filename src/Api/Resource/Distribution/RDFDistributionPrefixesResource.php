<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\RDF\RDFDistribution;

class RDFDistributionPrefixesResource implements ApiResource
{
    /** @var RDFDistribution */
    private $distribution;

    public function __construct(RDFDistribution $distribution)
    {
        $this->distribution = $distribution;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->distribution->getPrefixes() as $prefix) {
            $data[] = (new RDFDistributionPrefixResource($prefix))->toArray();
        }

        return $data;
    }
}
