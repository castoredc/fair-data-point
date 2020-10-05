<?php
declare(strict_types=1);

namespace App\Message\Dataset;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;

class GetPaginatedDatasetsCommand
{
    private ?Catalog $catalog;

    private ?string $search = null;

    /** @var StudyType[]|null */
    private ?array $studyType = null;

    /** @var MethodType[]|null */
    private ?array $methodType = null;

    /** @var string[]|null */
    private ?array $country = null;

    private int $perPage;

    private int $page;

    /** @var string[]|null */
    private ?array $hideCatalogs = null;

    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     * @param string[]|null     $hideCatalogs
     */
    public function __construct(?Catalog $catalog, ?string $search, ?array $studyType, ?array $methodType, ?array $country, ?array $hideCatalogs, int $perPage, int $page)
    {
        $this->catalog = $catalog;
        $this->search = $search;
        $this->studyType = $studyType;
        $this->methodType = $methodType;
        $this->country = $country;
        $this->hideCatalogs = $hideCatalogs;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function getCatalog(): ?Catalog
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

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return string[]|null
     */
    public function getHideCatalogs(): ?array
    {
        return $this->hideCatalogs;
    }
}
