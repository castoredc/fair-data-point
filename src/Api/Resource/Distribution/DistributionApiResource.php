<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
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

        if ($this->distribution instanceof RDFDistribution) {
            $accessUrl = $this->distribution->getRDFUrl();
            $downloadUrl = $this->distribution->getRDFUrl() . '/?download=1';
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
            'issued' => $this->distribution->getIssued(),
            'modified' => $this->distribution->getModified(),
            'accessRights' => $this->distribution->getAccessRights(),
            'download_url' => $downloadUrl,
        ];
    }
}
