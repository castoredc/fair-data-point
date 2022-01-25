<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Security\User;

class FindCatalogsByUserCommand
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
