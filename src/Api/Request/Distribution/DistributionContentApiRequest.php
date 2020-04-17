<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\GroupedApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DistributionContentApiRequest extends GroupedApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $value;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
        $this->value = $this->getFromData('value');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
