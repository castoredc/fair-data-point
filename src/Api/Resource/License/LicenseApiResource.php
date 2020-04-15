<?php
declare(strict_types=1);

namespace App\Api\Resource\License;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\License;

class LicenseApiResource implements ApiResource
{
    /** @var License */
    private $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'value' => $this->license->getSlug(),
            'url' => $this->license->getUrl()->getValue(),
            'label' => $this->license->getName(),
        ];
    }
}
