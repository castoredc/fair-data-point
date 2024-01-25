<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\DataSpecification\DataModel\DataModel;

class UpdateDataModelCommand
{
    private DataModel $dataModel;

    private string $title;

    private ?string $description = null;

    public function __construct(DataModel $dataModel, string $title, ?string $description)
    {
        $this->dataModel = $dataModel;
        $this->title = $title;
        $this->description = $description;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
