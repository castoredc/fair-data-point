<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Catalog;

class CatalogBrandApiResource implements ApiResource
{
    /** @var Catalog */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->catalog->getTitle()->toArray(),
            'accessingData' => $this->catalog->isSubmissionAccessingData(),
        ];
    }
}
