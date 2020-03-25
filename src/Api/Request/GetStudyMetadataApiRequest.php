<?php

namespace App\Api\Request;

class GetStudyMetadataApiRequest extends SingleApiRequest
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