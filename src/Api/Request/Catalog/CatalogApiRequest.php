<?php
declare(strict_types=1);

namespace App\Api\Request\Catalog;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CatalogApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $slug;

    /**
     * @var bool
     * @Assert\NotNull
     * @Assert\Type("bool")
     */
    private $acceptSubmissions;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $submissionAccessesData;

    protected function parse(): void
    {
        $this->slug = $this->getFromData('slug');
        $this->acceptSubmissions = $this->getFromData('acceptSubmissions');
        $this->submissionAccessesData = $this->getFromData('submissionAccessesData');
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
