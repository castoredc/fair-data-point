<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Study;

class StudiesApiResource implements ApiResource
{
    /** @var Study[] */
    private array $studies;

    private bool $isAdmin;

    /** @param Study[] $studies */
    public function __construct(array $studies, bool $isAdmin)
    {
        $this->studies = $studies;
        $this->isAdmin = $isAdmin;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->studies as $study) {
            $data[] = (new StudyApiResource($study, $this->isAdmin))->toArray();
        }

        return $data;
    }
}
