<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class GetMetadataModelRDFPreviewCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion)
    {
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
