<?php
declare(strict_types=1);

namespace App\Message\Dataset;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;

class GetDatasetsCommand
{
    /** @var Catalog */
    private $catalog;

    /** @var string|null */
    private $search;

    /** @var StudyType[]|null */
    private $studyType;

    /** @var MethodType[]|null */
    private $methodType;

    /** @var string[]|null */
    private $country;

    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    public function __construct(Catalog $catalog, ?string $search, ?array $studyType, ?array $methodType, ?array $country)
    {
        $this->catalog = $catalog;
        $this->search = $search;
        $this->studyType = $studyType;
        $this->methodType = $methodType;
        $this->country = $country;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @return StudyType[]|null
     */
    public function getStudyType(): ?array
    {
        return $this->studyType;
    }

    /**
     * @return MethodType[]|null
     */
    public function getMethodType(): ?array
    {
        return $this->methodType;
    }

    /**
     * @return string[]|null
     */
    public function getCountry(): ?array
    {
        return $this->country;
    }
}
