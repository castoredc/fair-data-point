<?php
declare(strict_types=1);

namespace App\Message\Study\Provenance;

use App\Entity\Study;

class ClearStudyCentersCommand
{
    private Study $study;

    public function __construct(
        Study $study
    ) {
        $this->study = $study;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
