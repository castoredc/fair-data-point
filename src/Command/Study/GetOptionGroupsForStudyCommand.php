<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Castor\CastorStudy;

class GetOptionGroupsForStudyCommand
{
    public function __construct(private CastorStudy $study)
    {
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }
}
