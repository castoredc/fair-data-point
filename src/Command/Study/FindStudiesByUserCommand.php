<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Security\User;

class FindStudiesByUserCommand
{
    private User $user;

    private bool $loadFromCastor;

    private bool $hideExistingStudies;

    public function __construct(User $user, bool $loadFromCastor, bool $hideExistingStudies = false)
    {
        $this->user = $user;
        $this->loadFromCastor = $loadFromCastor;
        $this->hideExistingStudies = $hideExistingStudies;
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
