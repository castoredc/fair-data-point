<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StudyMetadataFilterApiRequest extends SingleApiRequest
{
    public const DEFAULT_PER_PAGE = 25;

    #[Assert\Type('integer')]
    private int $perPage;

    #[Assert\Type('integer')]
    private int $page;

    /** @var string[]|null */
    #[Assert\Type('array')]
    private ?array $hideCatalogs = null;

    protected function parse(): void
    {
        $this->perPage = (int) $this->getFromQuery('perPage');
        $this->page = (int) $this->getFromQuery('page');
        $this->hideCatalogs = $this->getFromQuery('hideCatalogs');
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
