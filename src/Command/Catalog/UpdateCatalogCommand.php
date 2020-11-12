<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Entity\FAIRData\Catalog;

class UpdateCatalogCommand
{
    private Catalog $catalog;

    private string $slug;

    private bool $acceptSubmissions;

    private ?bool $submissionAccessesData = null;

    public function __construct(Catalog $catalog, string $slug, bool $acceptSubmissions, ?bool $submissionAccessesData)
    {
        $this->catalog = $catalog;
        $this->slug = $slug;
        $this->acceptSubmissions = $acceptSubmissions;
        $this->submissionAccessesData = $submissionAccessesData;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
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
