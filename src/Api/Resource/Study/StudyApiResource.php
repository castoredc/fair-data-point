<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Study;

class StudyApiResource implements ApiResource
{
    /** @var Study */
    private $study;

    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->study->getId(),
            'name' => $this->study->getName(),
            'slug' => $this->study->getSlug(),
        ];
    }
}
