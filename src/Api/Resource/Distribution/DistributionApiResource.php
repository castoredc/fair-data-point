<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\DataModelVersionApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Service\UriHelper;

class DistributionApiResource implements ApiResource
{
    private Distribution $distribution;

    private UriHelper $uriHelper;

    public function __construct(Distribution $distribution, UriHelper $uriHelper)
    {
        $this->distribution = $distribution;
        $this->uriHelper = $uriHelper;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $distribution = [
            'relativeUrl' => $this->distribution->getRelativeUrl(),
            'id' => $this->distribution->getId(),
            'slug' => $this->distribution->getSlug(),
            'hasMetadata' => $this->distribution->hasMetadata(),
            'hasContents' => $this->distribution->hasContents(),
            'license' => $this->distribution->getLicense() !== null ? $this->distribution->getLicense()->getSlug() : null,
            'study' => $this->distribution->getDataset()->getStudy() !== null ? (new StudyApiResource($this->distribution->getDataset()->getStudy()))->toArray() : null,
            'hasApiUser' => $this->distribution->getApiUser() !== null,
            'published' => $this->distribution->hasContents() ? $this->distribution->getContents()->isPublished() : false,
        ];

        if ($this->distribution->hasMetadata()) {
            $metadata = $this->distribution->getLatestMetadata();

            $distribution['metadata'] = [
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

        if ($this->distribution->hasContents()) {
            $contents = $this->distribution->getContents();
            $distribution['accessRights'] = $contents->getAccessRights();

            if ($contents instanceof RDFDistribution) {
                $distribution['fullUrl'] = $this->uriHelper->getUri($contents) . '/';
                $distribution['accessUrl'] = $contents->getRelativeUrl();
                $distribution['downloadUrl'] = $contents->getRelativeUrl() . '/?download=1';
                $distribution['type'] = 'rdf';
                $distribution['dataModel'] = (new DataModelVersionApiResource($contents->getCurrentDataModelVersion()))->toArray();
                $distribution['isCached'] = $contents->isCached();
            }

            if ($contents instanceof CSVDistribution) {
                $distribution['downloadUrl'] = $contents->getRelativeUrl();
                $distribution['type'] = 'csv';
                $distribution['includeAllData'] = $contents->isIncludeAll();
                $distribution['isCached'] = false;
            }
        }

        return $distribution;
    }
}
