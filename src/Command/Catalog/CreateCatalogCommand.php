<?php
declare(strict_types=1);

namespace App\Command\Catalog;

class CreateCatalogCommand
{
    private string $slug;

    private bool $acceptSubmissions;

    private ?bool $submissionAccessesData = null;

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
