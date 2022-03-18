<?php
declare(strict_types=1);

namespace App\Api\Resource\License;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\License;

class LicensesApiResource implements ApiResource
{
    /** @var License[] */
    private array $licenses;

    /** @param License[] $licenses */
    public function __construct(array $licenses)
    {
        $this->licenses = $licenses;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->licenses as $license) {
            $data[] = (new LicenseApiResource($license))->toArray();
        }

        return $data;
    }
}
