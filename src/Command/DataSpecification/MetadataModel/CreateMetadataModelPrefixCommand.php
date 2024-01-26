<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelPrefixCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelPrefixCommand extends CreateModelPrefixCommand
{
    private MetadataModelVersion $metadataModelVersion;

    public function __construct(MetadataModelVersion $metadataModelVersion, string $prefix, string $uri)
    {
        parent::__construct($prefix, $uri);

        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
