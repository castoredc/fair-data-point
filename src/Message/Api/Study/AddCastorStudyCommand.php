<?php

namespace App\Message\Api\Study;

use App\Entity\Enum\StudyType;
use App\Security\CastorUser;
use DateTimeImmutable;

class AddCastorStudyCommand
{
    /** @var string
     */
    private $studyId;

    /**
     * @var CastorUser
     */
    private $user;

    /**
     * CreateStudyCommand constructor.
     *
     * @param string     $studyId
     * @param CastorUser $user
     */
    public function __construct(
        string $studyId,
        CastorUser $user
    ) {
        $this->studyId = $studyId;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }

    /**
     * @return CastorUser
     */
    public function getUser(): CastorUser
    {
        return $this->user;
    }
}