<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelVersion;

class GetDataModelRDFPreviewCommand
{
    /** @var DataModelVersion */
    private $dataModel;

    public function __construct(DataModelVersion $dataModel)
    {
        $this->dataModel = $dataModel;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }
}
