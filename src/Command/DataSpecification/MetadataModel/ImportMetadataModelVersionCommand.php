<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\ImportVersionCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportMetadataModelVersionCommand extends ImportVersionCommand
{
    public function __construct(private MetadataModel $metadataModel, UploadedFile $file, Version $version)
    {
        parent::__construct($file, $version);
    }

    public function getMetadataModel(): MetadataModel
    {
        return $this->metadataModel;
    }
}
