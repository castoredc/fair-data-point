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
            'downloadUrl' => null,
            'type' => null,
        ];

        $contents = $this->distribution->getContents();

        if ($contents instanceof RDFDistribution) {
            $data['accessUrl'] = $contents->getRDFUrl();
            $data['downloadUrl'] = $contents->getRDFUrl() . '/?download=1';
            $data['type'] = 'rdf';
        }

        if ($contents instanceof CSVDistribution) {
            $data['downloadUrl'] = $contents->getAccessUrl();
            $data['type'] = 'csv';
        }

        if ($this->isAdmin) {
            $data['studyId'] = $this->distribution->getDataset()->getStudy()->getId();
        }

        return $data;
    }
}
