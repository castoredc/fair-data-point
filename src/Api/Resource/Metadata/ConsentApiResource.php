<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata;

class ConsentApiResource implements ApiResource
{
    private StudyMetadata $studyMetadata;

    public function __construct(StudyMetadata $studyMetadata)
    {
        $this->studyMetadata = $studyMetadata;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'publish' => $this->studyMetadata->hasConsentPublish(),
            'socialMedia' => $this->studyMetadata->hasConsentSocialMedia(),
        ];
    }
}
