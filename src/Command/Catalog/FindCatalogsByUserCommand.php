<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Security\User;

class FindCatalogsByUserCommand
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
