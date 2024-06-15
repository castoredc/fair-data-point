<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\Metadata\MetadataApiResource;
use App\Api\Resource\RoleBasedApiResource;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;

class StudyApiResource extends RoleBasedApiResource
{
    public function __construct(private Study $study, bool $isAdmin = false)
    {
        parent::setAdmin($isAdmin);
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $sourceServer = null;

        if ($this->study instanceof CastorStudy) {
            $sourceServer = $this->study->getServer()?->getId();
        }

        return [
            'relativeUrl' => $this->study->getRelativeUrl(),
            'id' => $this->study->getId(),
            'name' => $this->study->getName(),
            'slug' => $this->study->getSlug(),
            'defaultMetadataModel' => $this->study->getDefaultMetadataModel()?->getId(),
            'hasMetadata' => $this->study->hasMetadata(),
            'metadata' => $this->study->hasMetadata() ? (new MetadataApiResource($this->study->getLatestMetadata()))->toArray() : null,
            'source' => $this->study->getSource()->toString(),
            'sourceId' => $this->study->getSourceId(),
            'sourceServer' => $sourceServer,
            'published' => $this->study->isPublished(),
            'count' => [
                'dataset' => $this->study->getDatasets()->count(),
            ],
        ];
    }
}
