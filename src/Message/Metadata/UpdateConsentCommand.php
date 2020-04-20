<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Castor\Study;

class UpdateConsentCommand
{
    /** @var Study */
    private $study;

    /** @var bool */
    private $publish;

    /** @var bool */
    private $socialMedia;

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
