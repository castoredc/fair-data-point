<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Security\User;

class FindDistributionsByUserCommand
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
