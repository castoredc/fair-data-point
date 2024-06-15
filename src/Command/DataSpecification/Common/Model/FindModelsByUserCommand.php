<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Security\User;

abstract class FindModelsByUserCommand
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
