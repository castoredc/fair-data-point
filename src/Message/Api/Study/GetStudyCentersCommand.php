<?php

namespace App\Message\Api\Study;

use App\Entity\Enum\StudyType;
use App\Security\CastorUser;
use DateTimeImmutable;

class GetStudyCentersCommand
{
    /** @var string
     */
    private $studyId;

    /**
     * CreateStudyCommand constructor.
     *
     * @param string     $studyId
     */
    public function __construct(
        string $studyId
    ) {
        $this->studyId = $studyId;
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }
}