<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Security\CastorUser;

class GetStudyMetadataCommand
{
    /** @var string */
    private $studyId;

    /** @var CastorUser */
    private $user;

    public function __construct(
        string $studyId,
        CastorUser $user
    ) {
        $this->studyId = $studyId;
        $this->user = $user;
    }

    public function getStudyId(): string
    {
        return $this->studyId;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
