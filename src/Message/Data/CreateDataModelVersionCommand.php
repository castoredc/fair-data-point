<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Enum\VersionType;

class CreateDataModelVersionCommand
{
    private DataModel $dataModel;

    private VersionType $versionType;

    public function __construct(DataModel $dataModel, VersionType $versionType)
    {
        $this->dataModel = $dataModel;
        $this->versionType = $versionType;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }
}
