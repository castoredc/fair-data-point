<?php
declare(strict_types=1);

namespace App\Api\Request;

class GetStudyMetadataApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $studyId;

    protected function parse(): void
    {
        $this->studyId = $this->getFromData('studyId');
    }

    public function getStudyId(): string
    {
        return $this->studyId;
    }
}
