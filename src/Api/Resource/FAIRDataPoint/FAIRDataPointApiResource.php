<?php
declare(strict_types=1);

namespace App\Api\Resource\FAIRDataPoint;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\FAIRDataPoint;

class FAIRDataPointApiResource implements ApiResource
{
    /** @var FAIRDataPoint */
    private $fairDataPoint;

    public function __construct(FAIRDataPoint $fairDataPoint)
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'access_url' => $this->fairDataPoint->getAccessUrl(),
            'relative_url' => $this->fairDataPoint->getRelativeUrl(),
            'iri' => $this->fairDataPoint->getIri(),
            'title' => $this->fairDataPoint->getTitle()->toArray(),
            'version' => $this->fairDataPoint->getVersion(),
            'description' => $this->fairDataPoint->getDescription()->toArray(),
            'publishers' => [],
            'language' => $this->fairDataPoint->getLanguage()->toArray(),
            'license' => $this->fairDataPoint->getLicense()->toArray(),
        ];
    }
}
