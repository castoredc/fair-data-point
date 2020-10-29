<?php
declare(strict_types=1);

namespace App\Api\Resource\FAIRDataPoint;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\FAIRDataPoint;

class FAIRDataPointApiResource implements ApiResource
{
    private FAIRDataPoint $fairDataPoint;

    public function __construct(FAIRDataPoint $fairDataPoint)
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $fdp = [
            'relativeUrl' => $this->fairDataPoint->getRelativeUrl(),
            'iri' => $this->fairDataPoint->getIri(),
            'hasMetadata' => $this->fairDataPoint->hasMetadata(),
        ];

        if ($this->fairDataPoint->hasMetadata()) {
            $metadata = $this->fairDataPoint->getLatestMetadata();

            $fdp['metadata'] = [
                'title' => $metadata->getTitle()->toArray(),
                'version' => [
                    'metadata' => $metadata->getVersion()->getValue(),
                ],
                'description' => $metadata->getDescription()->toArray(),
                'publishers' => (new AgentsApiResource($metadata->getPublishers()->toArray()))->toArray(),
                'language' => $metadata->getLanguage() !== null ? $metadata->getLanguage()->getCode() : null,
                'license' => $metadata->getLicense() !== null ? $metadata->getLicense()->getSlug() : null,
                'created' => $metadata->getCreatedAt(),
                'updated' => $metadata->getUpdatedAt(),
            ];
        }

        return $fdp;
    }
}
