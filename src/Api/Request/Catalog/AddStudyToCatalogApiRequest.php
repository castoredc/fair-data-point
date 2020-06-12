<?php
declare(strict_types=1);

namespace App\Api\Request\Catalog;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class AddStudyToCatalogApiRequest extends SingleApiRequest
{
    /**
     * @var string|null
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
