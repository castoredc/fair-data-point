<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Api\Resource\Terminology\OntologyConceptsApiResource;
use App\Entity\FAIRData\Catalog;
use function count;
use const DATE_ATOM;

class CatalogApiResource implements ApiResource
{
    private Catalog $catalog;

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
            'submissionAccessesData' => $this->catalog->isSubmissionAccessingData(),
            'hasMetadata' => $this->catalog->hasMetadata(),
            'count' => [
                'study' => count($this->catalog->getStudies(false)),
                'dataset' => count($this->catalog->getDatasets(false)),
            ],
        ];

        if ($this->catalog->hasMetadata()) {
            $first = $this->catalog->getFirstMetadata();
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
                'homepage' => $metadata->getHomepage() !== null ? $metadata->getHomepage()->getValue() : null,
                'logo' => $metadata->getLogo() !== null ? $metadata->getLogo()->getValue() : null,
                'themeTaxonomy' => (new OntologyConceptsApiResource($metadata->getThemeTaxonomies()->toArray()))->toArray(),
                'issued' => $first->getCreatedAt()->format(DATE_ATOM),
                'modified' => $metadata->getUpdatedAt() !== null ? $metadata->getUpdatedAt()->format(DATE_ATOM) : $metadata->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return $catalog;
    }
}
