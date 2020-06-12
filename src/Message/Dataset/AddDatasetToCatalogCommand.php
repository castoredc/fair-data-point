<?php
declare(strict_types=1);

namespace App\Message\Dataset;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;

class AddDatasetToCatalogCommand
{
    /** @var Dataset */
    private $dataset;

    /** @var Catalog */
    private $catalog;

    public function __construct(Dataset $dataset, Catalog $catalog)
    {
        $this->dataset = $dataset;
        $this->catalog = $catalog;
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
