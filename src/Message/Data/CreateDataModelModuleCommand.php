<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModel;

class CreateDataModelModuleCommand
{
    /** @var DataModel */
    private $dataModel;

    /** @var string */
    private $title;

    /** @var int */
    private $order;

    public function __construct(DataModel $dataModel, string $title, int $order)
    {
        $this->dataModel = $dataModel;
        $this->title = $title;
        $this->order = $order;
    }

    public function getDataModel(): DataModel
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
