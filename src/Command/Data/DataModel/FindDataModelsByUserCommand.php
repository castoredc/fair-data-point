<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Security\User;

class FindDataModelsByUserCommand
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
