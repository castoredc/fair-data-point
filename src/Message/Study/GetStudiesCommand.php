<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Security\CastorUser;

class GetStudiesCommand
{
    /** @var CastorUser */
    private $user;

    public function __construct(CastorUser $user)
    {
        $this->user = $user;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
