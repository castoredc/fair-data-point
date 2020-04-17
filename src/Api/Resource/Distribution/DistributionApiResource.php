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

    /** @var bool */
    private $isAdmin;

    public function __construct(Distribution $distribution, bool $isAdmin)
    {
        $this->distribution = $distribution;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'accessUrl' => null,
            'relativeUrl' => $this->distribution->getRelativeUrl(),
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
            'downloadUrl' => null,
            'type' => null,
        ];

        if ($this->distribution instanceof RDFDistribution) {
            $data['accessUrl'] = $this->distribution->getRDFUrl();
            $data['downloadUrl'] = $this->distribution->getRDFUrl() . '/?download=1';
            $data['type'] = 'rdf';
        }

        if ($this->distribution instanceof CSVDistribution) {
            $data['downloadUrl'] = $this->distribution->getAccessUrl();
            $data['type'] = 'csv';
        }

        if ($this->isAdmin) {
            $data['studyId'] = $this->distribution->getDataset()->getStudy()->getId();
        }

        return $data;
    }
}
