<?php
declare(strict_types=1);

namespace App\Message\Dataset;

use App\Entity\Study;

class GetDatasetsByStudyCommand
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
