<?php
declare(strict_types=1);

namespace App\Message\Catalog;

use App\Entity\FAIRData\Catalog;

class UpdateCatalogCommand
{
    /** @var Catalog */
    private $catalog;

    /** @var string */
    private $slug;

    /** @var bool */
    private $acceptSubmissions;

    /** @var bool|null */
    private $submissionAccessesData;

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
