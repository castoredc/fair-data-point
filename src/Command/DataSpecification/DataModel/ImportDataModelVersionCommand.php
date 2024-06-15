<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\ImportVersionCommand;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportDataModelVersionCommand extends ImportVersionCommand
{
    public function __construct(private DataModel $dataModel, UploadedFile $file, Version $version)
    {
        parent::__construct($file, $version);
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }
}
