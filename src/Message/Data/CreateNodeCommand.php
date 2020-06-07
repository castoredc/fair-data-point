<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;

class CreateNodeCommand
{
    /** @var DataModel */
    private $dataModel;

    /** @var NodeType */
    private $type;

    /** @var string */
    private $title;

    /** @var string|null */
    private $description;

    /** @var string */
    private $value;

    /** @var XsdDataType */
    private $dataType;

    public function __construct(DataModel $dataModel, NodeType $type, string $title, ?string $description, string $value, ?XsdDataType $dataType)
    {
        $this->dataModel = $dataModel;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
        $this->dataType = $dataType;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function getType(): NodeType
    {
        return $this->type;
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

    public function getDataType(): XsdDataType
    {
        return $this->dataType;
    }
}
