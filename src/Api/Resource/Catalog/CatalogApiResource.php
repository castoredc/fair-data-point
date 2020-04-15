<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

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
        return [
            'access_url' => $this->catalog->getAccessUrl(),
            'relative_url' => $this->catalog->getRelativeUrl(),
            'id' => $this->catalog->getId(),
            'slug' => $this->catalog->getSlug(),
            'title' => $this->catalog->getTitle()->toArray(),
            'version' => $this->catalog->getVersion(),
            'description' => $this->catalog->getDescription()->toArray(),
            'publishers' => [],
            'language' => $this->catalog->getLanguage()->toArray(),
            'license' => $this->catalog->getLicense()->toArray(),
            'created' => $this->catalog->getCreated(),
            'updated' => $this->catalog->getUpdated(),
            'homepage' => $this->catalog->getHomepage() !== null ? $this->catalog->getHomepage()->getValue() : null,
            'logo' => $this->catalog->getLogo() !== null ? $this->catalog->getLogo()->getValue() : null,
            'acceptSubmissions' => $this->catalog->isAcceptingSubmissions(),
        ];
    }
}
