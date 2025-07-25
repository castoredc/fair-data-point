<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\DataDictionary\DataDictionaryVersionApiResource;
use App\Api\Resource\DataSpecification\DataModel\DataModelVersionApiResource;
use App\Api\Resource\Metadata\MetadataApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Service\UriHelper;

class DistributionApiResource implements ApiResource
{
    public function __construct(private Distribution $distribution, private UriHelper $uriHelper)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $distribution = [
            'relativeUrl' => $this->distribution->getRelativeUrl(),
            'id' => $this->distribution->getId(),
            'slug' => $this->distribution->getSlug(),
            'defaultMetadataModel' => $this->distribution->getDefaultMetadataModel()?->getId(),
            'hasMetadata' => $this->distribution->hasMetadata(),
            'metadata' => $this->distribution->hasMetadata() ? (new MetadataApiResource($this->distribution->getLatestMetadata()))->toArray() : null,
            'hasContents' => $this->distribution->hasContents(),
            'license' => $this->distribution->getLicense()?->getSlug(),
            'study' => $this->distribution->getDataset()->getStudy() !== null ? (new StudyApiResource($this->distribution->getDataset()->getStudy()))->toArray() : null,
            'hasApiUser' => $this->distribution->getApiUser() !== null,
            'published' => $this->distribution->isPublished(),
        ];

        if ($this->distribution->hasContents()) {
            $contents = $this->distribution->getContents();
            $distribution['cached'] = $contents->isCached();
            $distribution['public'] = $contents->isPublic();
            $distribution['type'] = $contents->getType();

            if ($contents instanceof RDFDistribution) {
                $distribution['fullUrl'] = $this->uriHelper->getUri($contents) . '/';
                $distribution['accessUrl'] = $contents->getRelativeUrl();
                $distribution['downloadUrl'] = $contents->getRelativeUrl() . '/?download=1';
                $distribution['dataModel'] = (new DataModelVersionApiResource($contents->getCurrentDataModelVersion()))->toArray();
            }

            if ($contents instanceof CSVDistribution) {
                $distribution['downloadUrl'] = $contents->getRelativeUrl();
                $distribution['dataDictionary'] = (new DataDictionaryVersionApiResource($contents->getCurrentDataDictionaryVersion()))->toArray();
            }
        }

        return $distribution;
    }
}
