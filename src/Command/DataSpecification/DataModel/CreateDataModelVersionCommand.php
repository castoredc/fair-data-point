<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateModelVersionCommand;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\Enum\VersionType;

class CreateDataModelVersionCommand extends CreateModelVersionCommand
{
    public function __construct(private DataModel $dataModel, VersionType $versionType)
    {
        parent::__construct($versionType);
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }
}
