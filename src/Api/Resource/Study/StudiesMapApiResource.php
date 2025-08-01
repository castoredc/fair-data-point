<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Study;

class StudiesMapApiResource implements ApiResource
{
    /** @param Study[] $studies */
    public function __construct(private array $studies)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->studies as $study) {
            if (! $study->hasMetadata()) {
                continue;
            }

            foreach ($study->getLatestMetadata()->getOrganizations() as $organization) {
                if (! $organization->hasCoordinates()) {
                    continue;
                }

                $data[] = (new StudyMapApiResource($study, $organization))->toArray();
            }
        }

        return $data;
    }
}
