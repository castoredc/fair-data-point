<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Study;

class UpdateConsentCommand
{
    private Study $study;

    private bool $publish;

    private bool $socialMedia;

    public function __construct(Study $study, bool $publish, bool $socialMedia)
    {
        $this->study = $study;
        $this->publish = $publish;
        $this->socialMedia = $socialMedia;
    }

    public function getStudy(): Study
    {
        return $this->study;
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
