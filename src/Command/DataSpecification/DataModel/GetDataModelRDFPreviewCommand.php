<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelVersion;

class GetDataModelRDFPreviewCommand
{
    public function __construct(private DataModelVersion $dataModelVersion)
    {
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
