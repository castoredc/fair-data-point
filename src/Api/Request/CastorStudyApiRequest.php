<?php

namespace App\Api\Request;

use App\Entity\Enum\StudyType;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CastorStudyApiRequest extends SingleApiRequest
{
    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $studyId;

    protected function parse(): void
    {
        $this->studyId = $this->getFromData('studyId');
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }
}