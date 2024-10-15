<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class GetPaginatedStudiesCommand
{
    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     * @param string[]|null     $hideCatalogs
     */
    public function __construct(private int $perPage, private int $page, private ?Catalog $catalog = null, private ?Agent $agent = null, private ?string $search = null, private ?array $studyType = null, private ?array $methodType = null, private ?array $country = null, private ?array $hideCatalogs = null)
    {
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    /** @return StudyType[]|null */
    public function getStudyType(): ?array
    {
        return $this->studyType;
    }

    /** @return MethodType[]|null */
    public function getMethodType(): ?array
    {
        return $this->methodType;
    }

    /** @return string[]|null */
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

    /** @return string[]|null */
    public function getHideCatalogs(): ?array
    {
        return $this->hideCatalogs;
    }
}
