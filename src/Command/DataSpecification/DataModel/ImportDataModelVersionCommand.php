<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportDataModelVersionCommand
{
    private DataModel $dataModel;
    private UploadedFile $file;
    private Version $version;

    public function __construct(DataModel $dataModel, UploadedFile $file, Version $version)
    {
        $this->dataModel = $dataModel;
        $this->file = $file;
        $this->version = $version;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
