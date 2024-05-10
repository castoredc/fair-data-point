<?php
declare(strict_types=1);

namespace App\Command\Dataset;

use App\Entity\Study;

class GetDatasetsByStudyCommand
{
    public function __construct(private Study $study)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
