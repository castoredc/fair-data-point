<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Institute;

class InstituteApiResource implements ApiResource
{
    public function __construct(private Institute $institute)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->institute->getId(),
            'name' => $this->institute->getName(),
            'abbreviation' => $this->institute->getAbbreviation(),
            'code' => $this->institute->getCode(),
        ];
    }
}
