<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;

class InstitutesApiResource implements ApiResource
{
    public function __construct(private ArrayCollection $institutes)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->institutes as $institute) {
            $data[] = (new InstituteApiResource($institute))->toArray();
        }

        return $data;
    }
}
