<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

class ClearStudyContactsCommand
{
    /** @var string */
    private $studyId;

    public function __construct(
        string $studyId
    ) {
        $this->studyId = $studyId;
    }

    public function getStudyId(): string
    {
        return $this->studyId;
    }
}
