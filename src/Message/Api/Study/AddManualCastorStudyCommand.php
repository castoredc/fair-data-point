<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Security\CastorUser;

class AddManualCastorStudyCommand
{
    /** @var string */
    private $studyId;

    /** @var string */
    private $studyName;

    /** @var string */
    private $studySlug;

    /** @var CastorUser */
    private $user;

    public function __construct(
        string $studyId,
        string $studyName,
        string $studySlug,
        CastorUser $user
    ) {
        $this->studyId = $studyId;
        $this->studyName = $studyName;
        $this->studySlug = $studySlug;
        $this->user = $user;
    }

    public function getStudyId(): string
    {
        return $this->studyId;
    }

    public function getStudyName(): string
    {
        return $this->studyName;
    }

    public function getStudySlug(): string
    {
        return $this->studySlug;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
