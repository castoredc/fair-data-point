<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Study;

class StudiesApiResource implements ApiResource
{
    /** @var Study[] */
    private $studies;

    /**
     * @param Study[] $studies
     */
    public function __construct(array $studies)
    {
        $this->studies = $studies;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->studies as $study) {
            $data[] = (new StudyApiResource($study))->toArray();
        }

        return $data;
    }
}
