<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class RDFDistributionPrefixApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $prefix;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $uri;

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
