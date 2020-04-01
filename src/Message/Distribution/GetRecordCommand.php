<?php

namespace App\Message\Distribution;

use App\Entity\Castor\Study;
use App\Security\CastorUser;

class GetRecordCommand
{
    /** @var Study */
    private $study;

    /** @var string */
    private $recordId;

    /** @var CastorUser */
    private $user;
    
    public function __construct(Study $study, string $recordId, CastorUser $user)
    {
        $this->study = $study;
        $this->recordId = $recordId;
        $this->user = $user;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}