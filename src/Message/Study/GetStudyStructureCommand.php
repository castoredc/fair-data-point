<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Castor\CastorStudy;
use App\Security\CastorUser;

class GetStudyStructureCommand
{
    /** @var CastorStudy */
    private $study;

    /** @var CastorUser */
    private $user;

    public function __construct(
        CastorStudy $study,
        CastorUser $user
    ) {
        $this->study = $study;
        $this->user = $user;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
