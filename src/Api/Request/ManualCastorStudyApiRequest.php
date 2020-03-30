<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ManualCastorStudyApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $studyId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $studyName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $studySlug;

    protected function parse(): void
    {
        $this->studyId = $this->getFromData('studyId');
        $this->studyName = $this->getFromData('studyName');
        $this->studySlug = $this->getFromData('studySlug');
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
}
