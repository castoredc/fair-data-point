<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Study;

class PublishStudyCommand
{
    public function __construct(private Study $study)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
