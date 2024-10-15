<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\Common;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DataSpecificationPrefixApiRequest extends SingleApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $prefix;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $uri;

    protected function parse(): void
    {
        $this->prefix = $this->getFromData('prefix');
        $this->uri = $this->getFromData('uri');
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
