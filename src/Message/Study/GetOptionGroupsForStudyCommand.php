<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Castor\CastorStudy;

class GetOptionGroupsForStudyCommand
{
    private CastorStudy $study;

    public function __construct(CastorStudy $study)
    {
        $this->study = $study;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }
}
