<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class StudyMetadataFilterApiRequest extends SingleApiRequest
{
    public const DEFAULT_PER_PAGE = 25;

    #[Assert\Type('string')]
    private ?string $search = null;

    /** @var string[]|null */
    #[Assert\Type('array')]
    private ?array $studyType = null;

    /** @var string[]|null */
    #[Assert\Type('array')]
    private ?array $methodType = null;

    /**
     * @var string[]|null
     * @AppAssert\CountryArray
     */
    private ?array $country = null;

    #[Assert\Type('integer')]
    private int $perPage;

    #[Assert\Type('integer')]
    private int $page;

    /** @var string[]|null */
    #[Assert\Type('array')]
    private ?array $hideCatalogs = null;

    protected function parse(): void
    {
        $this->search = $this->getFromQuery('search');
        $this->studyType = $this->getFromQuery('studyType');
        $this->methodType = $this->getFromQuery('methodType');
        $this->country = $this->getFromQuery('country');
        $this->perPage = (int) $this->getFromQuery('perPage');
        $this->page = (int) $this->getFromQuery('page');
        $this->hideCatalogs = $this->getFromQuery('hideCatalogs');
    }

    public function getSearch(): ?string
    {
        return $this->search !== '' ? $this->search : null;
    }

    /** @return StudyType[]|null */
    public function getStudyType(): ?array
    {
        return $this->studyType !== null ? StudyType::fromArray($this->studyType) : null;
    }

    /** @return MethodType[]|null */
    public function getMethodType(): ?array
    {
        return $this->methodType !== null ? MethodType::fromArray($this->methodType) : null;
    }

    /** @return string[]|null */
    public function getCountry(): ?array
    {
        return $this->country;
    }

    public function getPerPage(): int
    {
        return $this->perPage !== 0 ? $this->perPage : self::DEFAULT_PER_PAGE;
    }

    public function getPage(): int
    {
        return $this->page !== 0 ? $this->page : 1;
    }

    /** @return string[]|null */
    public function getHideCatalogs(): ?array
    {
        return $this->hideCatalogs;
    }
}
