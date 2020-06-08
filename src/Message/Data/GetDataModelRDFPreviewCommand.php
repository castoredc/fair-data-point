<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModel;

class GetDataModelRDFPreviewCommand
{
    /** @var DataModel */
    private $dataModel;

    public function __construct(DataModel $dataModel)
    {
        $this->dataModel = $dataModel;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }
}
