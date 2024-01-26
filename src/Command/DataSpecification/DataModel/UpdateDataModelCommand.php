<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelCommand;
use App\Entity\DataSpecification\DataModel\DataModel;

class UpdateDataModelCommand extends UpdateModelCommand
{
    private DataModel $dataModel;

    public function __construct(DataModel $dataModel, string $title, ?string $description)
    {
        parent::__construct($title, $description);

        $this->dataModel = $dataModel;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }
}
