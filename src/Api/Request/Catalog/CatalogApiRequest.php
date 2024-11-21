<?php
declare(strict_types=1);

namespace App\Api\Request\Catalog;

use App\Api\Request\SingleApiRequest;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class CatalogApiRequest extends SingleApiRequest
{
    #[AppAssert\Slug(type: 'App\Entity\FAIRData\Catalog')]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $slug;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $defaultMetadataModel;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $acceptSubmissions;

    #[Assert\Type('bool')]
    private ?bool $submissionAccessesData = null;

    protected function parse(): void
    {
        $this->slug = $this->getFromData('slug');
        $this->defaultMetadataModel = $this->getFromData('defaultMetadataModel');
        $this->acceptSubmissions = $this->getFromData('acceptSubmissions');
        $this->submissionAccessesData = $this->getFromData('submissionAccessesData');
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDefaultMetadataModel(): string
    {
        return $this->defaultMetadataModel;
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
