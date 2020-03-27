<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;

class PublishStudyInCatalogCommand
{
    /** @var Study */
    private $study;

    /** @var Catalog */
    private $catalog;

    /**
     * @param string $catalog
     */
    public function __construct(Study $study, Catalog $catalog)
    {
        $this->study = $study;
        $this->catalog = $catalog;
    }

    /**
     * @return string
     */
    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
