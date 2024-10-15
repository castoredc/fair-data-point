<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use Symfony\Component\Validator\Constraints as Assert;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

class CatalogMetadataFilterApiRequest extends MetadataFilterApiRequest
{
    #[Assert\Type('bool')]
    private ?bool $acceptSubmissions;

    protected function parse(): void
    {
        parent::parse();

        $this->acceptSubmissions = $this->getFromQuery('acceptSubmissions') !== null ? filter_var($this->getFromQuery('acceptSubmissions'), FILTER_VALIDATE_BOOLEAN) : null;
    }

    public function getAcceptSubmissions(): ?bool
    {
        return $this->acceptSubmissions;
    }
}
