<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata\ParticipatingCenter;

class ParticipatingCentersApiResource implements ApiResource
{
    /** @var ParticipatingCenter[] */
    private array $participatingCenters;

    /** @param ParticipatingCenter[] $participatingCenters */
    public function __construct(array $participatingCenters)
    {
        $this->participatingCenters = $participatingCenters;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->participatingCenters as $participatingCenter) {
            $data[] = (new ParticipatingCenterApiResource($participatingCenter))->toArray();
        }

        return $data;
    }
}
