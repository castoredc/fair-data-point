<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class ImportVersionCommand
{
    private UploadedFile $file;
    private Version $version;

    public function __construct(UploadedFile $file, Version $version)
    {
        $this->file = $file;
        $this->version = $version;
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
