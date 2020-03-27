<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Security\CastorUser;

class FindStudiesByUserCommand
{
    /** @var CastorUser */
    private $user;

    /** @var bool */
    private $loadFromCastor;

    /** @var bool */
    private $hideExistingStudies;

    public function __construct(CastorUser $user, bool $loadFromCastor, bool $hideExistingStudies = false)
    {
        $this->user = $user;
        $this->loadFromCastor = $loadFromCastor;
        $this->hideExistingStudies = $hideExistingStudies;
    }

    public function getUser(): CastorUser
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
