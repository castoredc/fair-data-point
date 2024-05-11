<?php
declare(strict_types=1);

namespace App\Api\Resource\FAIRDataPoint;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\FAIRDataPoint;
use const DATE_ATOM;

class FAIRDataPointApiResource implements ApiResource
{
    public function __construct(private FAIRDataPoint $fairDataPoint)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $fdp = [
            'relativeUrl' => $this->fairDataPoint->getRelativeUrl(),
            'iri' => $this->fairDataPoint->getIri(),
            'hasMetadata' => $this->fairDataPoint->hasMetadata(),
            'count' => [
                'catalog' => $this->fairDataPoint->getCatalogs()->count(),
            ],
        ];

        if ($this->fairDataPoint->hasMetadata()) {
            $first = $this->fairDataPoint->getFirstMetadata();
            $metadata = $this->fairDataPoint->getLatestMetadata();

            $fdp['metadata'] = [
                'title' => $metadata->getLegacyTitle()->toArray(),
                'version' => [
                    'metadata' => $metadata->getVersion()->getValue(),
                ],
                'description' => $metadata->getDescription()->toArray(),
                'publishers' => (new AgentsApiResource($metadata->getPublishers()->toArray()))->toArray(),
                'language' => $metadata->getLanguage()?->getCode(),
                'license' => $metadata->getLicense()?->getSlug(),
                'issued' => $first->getCreatedAt()->format(DATE_ATOM),
                'modified' => $metadata->getUpdatedAt()?->format(DATE_ATOM) ?? $metadata->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return $fdp;
    }
}
