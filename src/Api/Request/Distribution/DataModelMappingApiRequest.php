<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DataModelMappingApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $node;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $element;

    protected function parse(): void
    {
        $this->node = $this->getFromData('node');
        $this->element = $this->getFromData('element');
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getElement(): string
    {
        return $this->element;
    }
}
