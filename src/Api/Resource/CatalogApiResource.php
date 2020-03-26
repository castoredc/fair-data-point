<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Catalog;

class CatalogApiResource implements ApiResource
{
    /** @var Catalog */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    public function toArray(): array
    {
        return $this->catalog->toBasicArray();
    }
}
