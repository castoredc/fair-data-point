<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class StudyMetadataFilterApiRequest extends SingleApiRequest
{
    const DEFAULT_PER_PAGE = 25;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $search;

    /**
     * @var string[]|null
     * @Assert\Type("array")
     */
    private $studyType;

    /**
     * @var string[]|null
     * @Assert\Type("array")
     */
    private $methodType;

    /**
     * @var string[]|null
     * @AppAssert\CountryArray
     */
    private $country;

    /**
     * @var int
     * @Assert\Type("integer")
     */
    private $perPage;

    /**
     * @var int
     * @Assert\Type("integer")
     */
    private $page;

    protected function parse(): void
    {
        $this->search = $this->getFromQuery('search');
        $this->studyType = $this->getFromQuery('studyType');
        $this->methodType = $this->getFromQuery('methodType');
        $this->country = $this->getFromQuery('country');
        $this->perPage = (int) $this->getFromQuery('perPage');
        $this->page = (int) $this->getFromQuery('page');
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
}
