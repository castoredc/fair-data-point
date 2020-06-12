<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Security\CastorUser;

class GetRecordCommand
{
    /** @var CastorStudy */
    private $study;

    /** @var string */
    private $recordId;

    /** @var CastorUser */
    private $user;

    public function __construct(CastorStudy $study, string $recordId, CastorUser $user)
    {
        $this->study = $study;
        $this->recordId = $recordId;
        $this->user = $user;
    }

    public function getStudy(): CastorStudy
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
