<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class FilterStudiesCommand
{
    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    public function __construct(private ?Catalog $catalog = null, private ?Agent $agent = null, private ?string $search = null, private ?array $studyType = null, private ?array $methodType = null, private ?array $country = null)
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
}
