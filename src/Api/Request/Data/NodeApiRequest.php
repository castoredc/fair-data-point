<?php
declare(strict_types=1);

namespace App\Api\Request\Data;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\XsdDataType;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class NodeApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $description;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $value;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $dataType;

    /**
     * @var bool
     * @Assert\Type("bool")
     */
    private $repeated;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->value = $this->getFromData('value');
        $this->dataType = $this->getFromData('dataType');
        $this->repeated = boolval($this->getFromData('repeated'));
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDataType(): ?XsdDataType
    {
        return $this->dataType !== null ? XsdDataType::fromString($this->dataType) : null;
    }

    public function isRepeated(): bool
    {
        return $this->repeated;
    }
}
