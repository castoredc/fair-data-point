<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\Metadata\ConsentApiResource;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Api\Resource\RoleBasedApiResource;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use const DATE_ATOM;

class StudyApiResource extends RoleBasedApiResource
{
    private Study $study;

    public function __construct(Study $study, bool $isAdmin = false)
    {
        $this->study = $study;

        parent::setAdmin($isAdmin);
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $firstMetadata = $this->study->getFirstMetadata();
        $dbMetadata = $this->study->getLatestMetadata();
        $hasMetadata = ($dbMetadata !== null);

        $metadata = $hasMetadata ? (new StudyMetadataApiResource($dbMetadata))->toArray() : null;
        $sourceServer = null;

        if ($this->study instanceof CastorStudy) {
            $sourceServer = $this->study->getServer() !== null ? $this->study->getServer()->getId() : null;
        }

        $data = [
            'id' => $this->study->getId(),
            'name' => $this->study->getName(),
            'slug' => $this->study->getSlug(),
            'hasMetadata' => $hasMetadata,
            'metadata' => $metadata,
            'source' => $this->study->getSource()->toString(),
            'sourceId' => $this->study->getSourceId(),
            'sourceServer' => $sourceServer,
            'published' => $this->study->isPublished(),
            'issued' => $hasMetadata ? $firstMetadata->getCreatedAt()->format(DATE_ATOM) : null,
            'modified' => $hasMetadata ? $dbMetadata->getCreatedAt()->format(DATE_ATOM) : null,
        ];

        if ($this->isAdmin) {
            $data['consent'] = $hasMetadata ? (new ConsentApiResource($dbMetadata))->toArray() : null;
        }

        return $data;
    }
}
