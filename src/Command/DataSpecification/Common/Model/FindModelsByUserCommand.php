<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Security\User;

abstract class FindModelsByUserCommand
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
