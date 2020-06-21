<?php
declare(strict_types=1);

namespace App\Message\Catalog;

class CreateCatalogCommand
{
    /** @var string */
    private $slug;

    /** @var bool */
    private $acceptSubmissions;

    /** @var bool|null */
    private $submissionAccessesData;

    public function __construct(string $slug, bool $acceptSubmissions, ?bool $submissionAccessesData)
    {
        $this->slug = $slug;
        $this->acceptSubmissions = $acceptSubmissions;
        $this->submissionAccessesData = $submissionAccessesData;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isAcceptSubmissions(): bool
    {
        return $this->acceptSubmissions;
    }

    public function isSubmissionAccessesData(): ?bool
    {
        return $this->submissionAccessesData;
    }
}
