<?php
declare(strict_types=1);

namespace App\Api\Request\Data;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DataModelModuleApiRequest extends SingleApiRequest
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

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private $repeated;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
        $this->repeated = $this->getFromData('repeated');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function isRepeated(): bool
    {
        return $this->repeated;
    }
}
