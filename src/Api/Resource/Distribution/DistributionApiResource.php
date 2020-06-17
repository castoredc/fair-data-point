<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class DistributionApiResource implements ApiResource
{
    /** @var Distribution */
    private $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $distribution = [
            'accessUrl' => $this->distribution->getAccessUrl(),
            'relativeUrl' => $this->distribution->getRelativeUrl(),
            'id' => $this->distribution->getId(),
            'slug' => $this->distribution->getSlug(),
            'hasMetadata' => $this->distribution->hasMetadata(),
            'hasContents' => $this->distribution->hasContents(),
            'license' => $this->distribution->getLicense() !== null ? $this->distribution->getLicense()->getSlug() : null,
            'study' => $this->distribution->getDataset()->getStudy() !== null ? $this->distribution->getDataset()->getStudy()->getId() : null
        ];

        if ($this->distribution->hasMetadata()) {
            $metadata = $this->distribution->getLatestMetadata();

            $distribution['metadata'] = [
                'title' => $metadata->getTitle()->toArray(),
                'version' => [
                    'metadata' => $metadata->getVersion()->getValue(),
                ],
                'description' => $metadata->getDescription()->toArray(),
                'publishers' => [],
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
                $distribution['accessUrl'] = $contents->getRDFUrl();
                $distribution['downloadUrl'] = $contents->getRDFUrl() . '/?download=1';
                $distribution['type'] = 'rdf';
                $distribution['dataModel'] = $contents->getDataModel()->getId();
            }

            if ($contents instanceof CSVDistribution) {
                $distribution['downloadUrl'] = $contents->getAccessUrl();
                $distribution['type'] = 'csv';
                $distribution['includeAllData'] = $contents->isIncludeAll();
            }
        }

        return $distribution;
    }
}
