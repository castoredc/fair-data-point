<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class FilterStudiesCommand
{
    private ?Catalog $catalog = null;

    private ?Agent $agent = null;

    private ?string $search = null;

    /** @var StudyType[]|null */
    private ?array $studyType = null;

    /** @var MethodType[]|null */
    private ?array $methodType = null;

    /** @var string[]|null */
    private ?array $country = null;

    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    public function __construct(?Catalog $catalog, ?Agent $agent, ?string $search, ?array $studyType, ?array $methodType, ?array $country)
    {
        $this->catalog = $catalog;
        $this->agent = $agent;
        $this->search = $search;
        $this->studyType = $studyType;
        $this->methodType = $methodType;
        $this->country = $country;
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
