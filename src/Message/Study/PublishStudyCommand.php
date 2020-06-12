<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Study;

class PublishStudyCommand
{
    /** @var Study */
    private $study;

    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
