<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateModelVersionCommand;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\Enum\VersionType;

class CreateDataModelVersionCommand extends CreateModelVersionCommand
{
    private DataModel $dataModel;

    public function __construct(DataModel $dataModel, VersionType $versionType)
    {
        parent::__construct($versionType);

        $this->dataModel = $dataModel;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }
}
