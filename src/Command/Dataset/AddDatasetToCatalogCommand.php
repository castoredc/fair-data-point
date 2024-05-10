<?php
declare(strict_types=1);

namespace App\Command\Dataset;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;

class AddDatasetToCatalogCommand
{
    public function __construct(private Dataset $dataset, private Catalog $catalog)
    {
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
