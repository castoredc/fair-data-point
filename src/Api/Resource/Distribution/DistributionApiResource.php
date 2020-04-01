<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Distribution\Distribution;

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
        return [
            'access_url' => $this->distribution->getAccessUrl(),
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
        ];
    }
}
