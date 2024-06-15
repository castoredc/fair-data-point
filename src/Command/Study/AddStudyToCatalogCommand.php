<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\FAIRData\Catalog;
use App\Entity\Study;

class AddStudyToCatalogCommand
{
    public function __construct(private Study $study, private Catalog $catalog)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
