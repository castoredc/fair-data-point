<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class RDFDistributionModuleApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private $order;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
