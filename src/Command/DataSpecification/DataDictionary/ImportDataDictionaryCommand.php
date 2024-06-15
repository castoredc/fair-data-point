<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportDataDictionaryCommand
{
    public function __construct(private DataDictionary $dataDictionary, private UploadedFile $file, private Version $version)
    {
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
