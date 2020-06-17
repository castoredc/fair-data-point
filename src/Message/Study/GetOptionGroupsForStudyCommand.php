<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Castor\CastorStudy;
use App\Security\CastorUser;

class GetOptionGroupsForStudyCommand
{
    /** @var CastorStudy */
    private $study;

    public function __construct(CastorStudy $study) {
        $this->study = $study;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }
}
