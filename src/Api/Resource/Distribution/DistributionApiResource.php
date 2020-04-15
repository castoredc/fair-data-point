<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistribution;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;

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
        $accessUrl = null;
        $downloadUrl = null;
        $type = null;

        if ($this->distribution instanceof RDFDistribution) {
            $accessUrl = $this->distribution->getRDFUrl();
            $downloadUrl = $this->distribution->getRDFUrl() . '/?download=1';
            $type = 'rdf';
        }

        if ($this->distribution instanceof CSVDistribution) {
            $downloadUrl = $this->distribution->getAccessUrl();
            $type = 'csv';
        }

        return [
            'access_url' => $accessUrl,
            'relative_url' => $this->distribution->getRelativeUrl(),
            'id' => $this->distribution->getId(),
            'slug' => $this->distribution->getSlug(),
            'title' => $this->distribution->getTitle()->toArray(),
            'version' => $this->distribution->getVersion(),
            'description' => $this->distribution->getDescription()->toArray(),
            'publishers' => [],
            'language' => $this->distribution->getLanguage()->toArray(),
            'license' => $this->distribution->getLicense()->toArray(),
            'created' => $this->distribution->getCreated(),
            'updated' => $this->distribution->getUpdated(),
            'accessRights' => $this->distribution->getAccessRights(),
            'download_url' => $downloadUrl,
            'type' => $type
        ];
    }
}
