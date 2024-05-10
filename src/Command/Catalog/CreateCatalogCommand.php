<?php
declare(strict_types=1);

namespace App\Command\Catalog;

class CreateCatalogCommand
{
    public function __construct(private string $slug, private bool $acceptSubmissions, private ?bool $submissionAccessesData = null)
    {
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
