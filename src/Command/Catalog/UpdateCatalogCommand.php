<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Entity\FAIRData\Catalog;

class UpdateCatalogCommand
{
    public function __construct(
        private Catalog $catalog,
        private string $slug,
        private bool $acceptSubmissions,
        private string $defaultMetadataModelId,
        private ?bool $submissionAccessesData = null,
    ) {
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

    public function getDefaultMetadataModelId(): string
    {
        return $this->defaultMetadataModelId;
    }
}
