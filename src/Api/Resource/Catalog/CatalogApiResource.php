<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use App\Api\Resource\Agent\Person\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Catalog;

class CatalogApiResource implements ApiResource
{
    /** @var Catalog */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $catalog = [
            'relativeUrl' => $this->catalog->getRelativeUrl(),
            'id' => $this->catalog->getId(),
            'slug' => $this->catalog->getSlug(),
            'acceptSubmissions' => $this->catalog->isAcceptingSubmissions(),
            'hasMetadata' => $this->catalog->hasMetadata(),
        ];

        if ($this->catalog->hasMetadata()) {
            $metadata = $this->catalog->getLatestMetadata();

            $catalog['metadata'] = [
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
                'homepage' => $metadata->getHomepage() !== null ? $metadata->getHomepage()->getValue() : null,
                'logo' => $metadata->getLogo() !== null ? $metadata->getLogo()->getValue() : null,
            ];
        }

        return $catalog;
    }
}
