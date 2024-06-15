<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Catalog;

class CatalogBrandApiResource implements ApiResource
{
    public function __construct(private Catalog $catalog)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $metadata = $this->catalog->getLatestMetadata();

        return [
            'name' => $metadata->getTitle()->toArray(),
            'accessingData' => $this->catalog->isSubmissionAccessingData(),
        ];
    }
}
