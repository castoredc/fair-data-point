<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportDataDictionaryCommand
{
    private DataDictionary $dataDictionary;
    private UploadedFile $file;
    private Version $version;

    public function __construct(DataDictionary $dataDictionary, UploadedFile $file, Version $version)
    {
        $this->dataDictionary = $dataDictionary;
        $this->file = $file;
        $this->version = $version;
    }

    public function getDataDictionary(): DataDictionary
    {
        return $this->dataDictionary;
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
