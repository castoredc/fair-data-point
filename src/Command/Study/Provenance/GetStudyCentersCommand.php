<?php
declare(strict_types=1);

namespace App\Command\Study\Provenance;

use App\Entity\Study;

class GetStudyCentersCommand
{
    public function __construct(
        private Study $study,
    ) {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
