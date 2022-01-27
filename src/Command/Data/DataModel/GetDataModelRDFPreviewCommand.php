<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\DataModelVersion;

class GetDataModelRDFPreviewCommand
{
    private DataModelVersion $dataModelVersion;

    public function __construct(DataModelVersion $dataModelVersion)
    {
        $this->dataModelVersion = $dataModelVersion;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
