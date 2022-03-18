<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Institute;
use Doctrine\Common\Collections\ArrayCollection;

class InstitutesApiResource implements ApiResource
{
    /** @var ArrayCollection<Institute> */
    private ArrayCollection $institutes;

    public function __construct(ArrayCollection $institutes)
    {
        $this->institutes = $institutes;
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
