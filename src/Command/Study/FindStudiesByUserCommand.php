<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Security\User;

class FindStudiesByUserCommand
{
    public function __construct(private User $user, private bool $loadFromCastor, private bool $hideExistingStudies = false)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLoadFromCastor(): bool
    {
        return $this->loadFromCastor;
    }

    public function getHideExistingStudies(): bool
    {
        return $this->hideExistingStudies;
    }
}
