<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;

class GetPaginatedStudiesCommand
{
    /** @var Catalog|null */
    private $catalog;

    /** @var string|null */
    private $search;

    /** @var StudyType[]|null */
    private $studyType;

    /** @var MethodType[]|null */
    private $methodType;

    /** @var string[]|null */
    private $country;

    /** @var int */
    private $perPage;

    /** @var int */
    private $page;

    /** @var string[]|null */
    private $hideCatalogs;

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
    public function getHideCatalogs()
    {
        return $this->hideCatalogs;
    }
}
