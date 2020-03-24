<?php

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

    /**
     * @return CastorUser
     */
    public function getUser(): CastorUser
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function getLoadFromCastor(): bool
    {
        return $this->loadFromCastor;
    }

    /**
     * @return bool
     */
    public function getHideExistingStudies(): bool
    {
        return $this->hideExistingStudies;
    }
}