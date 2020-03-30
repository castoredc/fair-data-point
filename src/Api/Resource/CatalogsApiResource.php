<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Catalog;

class CatalogsApiResource implements ApiResource
{
    /** @var Catalog[] */
    private $catalogs;

    /**
     * @param Catalog[] $catalogs
     */
    public function __construct(array $catalogs)
    {
        $this->catalogs = $catalogs;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->catalogs as $catalog) {
            $data[] = (new CatalogApiResource($catalog))->toArray();
        }

        return $data;
    }
}
