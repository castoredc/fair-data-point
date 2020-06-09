<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\ConsentApiResource;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Api\Resource\RoleBasedApiResource;
use App\Entity\Castor\Study;

class StudyApiResource extends RoleBasedApiResource
{
    /** @var Study */
    private $study;

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
        $dbMetadata = $this->study->getLatestMetadata();
        $hasMetadata = ($dbMetadata !== null);

        $metadata = $hasMetadata ? (new StudyMetadataApiResource($dbMetadata))->toArray() : null;

        $data = [
            'id' => $this->study->getId(),
            'name' => $this->study->getName(),
            'slug' => $this->study->getSlug(),
            'hasMetadata' => $hasMetadata,
            'metadata' => $metadata
        ];

        if($this->isAdmin) {
            $data['consent'] = $hasMetadata ? (new ConsentApiResource($dbMetadata))->toArray() : null;
        }

        return $data;
    }
}
