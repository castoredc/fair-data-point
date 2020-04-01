<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\Study;
use App\Security\CastorUser;

class GetRecordsCommand
{
    /** @var Study */
    private $study;

    /** @var CastorUser */
    private $user;

    public function __construct(Study $study, CastorUser $user)
    {
        $this->study = $study;
        $this->user = $user;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
