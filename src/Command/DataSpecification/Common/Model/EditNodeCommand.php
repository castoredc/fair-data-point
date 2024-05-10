<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Entity\Enum\XsdDataType;

abstract class EditNodeCommand
{
    public function __construct(private string $title, private ?string $description = null, private string $value, private ?XsdDataType $dataType = null)
    {
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
        return $this->dataType;
    }
}
