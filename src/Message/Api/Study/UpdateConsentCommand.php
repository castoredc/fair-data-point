<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Entity\Metadata\StudyMetadata;

class UpdateConsentCommand
{
    /** @var StudyMetadata */
    private $metadata;

    /** @var bool */
    private $publish;

    /** @var bool */
    private $socialMedia;

    public function __construct(StudyMetadata $metadata, bool $publish, bool $socialMedia)
    {
        $this->metadata = $metadata;
        $this->publish = $publish;
        $this->socialMedia = $socialMedia;
    }

    public function getMetadata(): StudyMetadata
    {
        return $this->metadata;
    }

    public function getPublish(): bool
    {
        return $this->publish;
    }

    public function getSocialMedia(): bool
    {
        return $this->socialMedia;
    }
}
