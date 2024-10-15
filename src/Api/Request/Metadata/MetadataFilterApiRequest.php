<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataFilterApiRequest extends SingleApiRequest
{
    public const DEFAULT_PER_PAGE = 25;

    #[Assert\Type('string')]
    private ?string $search = null;

    #[Assert\Type('integer')]
    private int $perPage;

    #[Assert\Type('integer')]
    private int $page;

    /**
     * @var string[]|null
     */
    #[Assert\Type('array')]
    private ?array $hideParents = null;

    protected function parse(): void
    {
        $this->search = $this->getFromQuery('search');
        $this->perPage = (int) $this->getFromQuery('perPage');
        $this->page = (int) $this->getFromQuery('page');
    }

    public function getSearch(): ?string
    {
        return $this->search !== '' ? $this->search : null;
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
    public function getHideParents(): ?array
    {
        return $this->hideParents;
    }
}
