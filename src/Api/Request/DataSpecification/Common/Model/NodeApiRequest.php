<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\Common\Model;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\XsdDataType;

abstract class NodeApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $title;

    /** @Assert\Type("string") */
    private ?string $description = null;

    /** @Assert\Type("string") */
    private string $value;

    /** @Assert\Type("string") */
    private ?string $dataType = null;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->value = $this->getFromData('value');
        $this->dataType = $this->getFromData('dataType');
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
}
