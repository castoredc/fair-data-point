<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelVersion;

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
