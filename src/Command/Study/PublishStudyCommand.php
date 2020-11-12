<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Study;

class PublishStudyCommand
{
    private Study $study;

    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
