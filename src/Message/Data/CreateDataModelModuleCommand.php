<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelVersion;

class CreateDataModelModuleCommand
{
    /** @var DataModelVersion */
    private $dataModel;

    /** @var string */
    private $title;

    /** @var int */
    private $order;

    public function __construct(DataModelVersion $dataModel, string $title, int $order)
    {
        $this->dataModel = $dataModel;
        $this->title = $title;
        $this->order = $order;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
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
