<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelPrefixCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelPrefixCommand extends CreateModelPrefixCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion, string $prefix, string $uri)
    {
        parent::__construct($prefix, $uri);
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
