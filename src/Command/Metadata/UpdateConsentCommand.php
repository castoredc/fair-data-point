<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Study;

class UpdateConsentCommand
{
    public function __construct(private Study $study, private bool $publish, private bool $socialMedia)
    {
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
