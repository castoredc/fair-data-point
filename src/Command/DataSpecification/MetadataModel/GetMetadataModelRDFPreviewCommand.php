<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class GetMetadataModelRDFPreviewCommand
{
    private MetadataModelVersion $metadataModelVersion;

    public function __construct(MetadataModelVersion $metadataModelVersion)
    {
        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
