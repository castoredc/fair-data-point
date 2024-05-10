<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;

class DeleteMetadataModelPrefixCommand
{
    public function __construct(private NamespacePrefix $metadataModelPrefix)
    {
    }

    public function getMetadataModelPrefix(): NamespacePrefix
    {
        return $this->metadataModelPrefix;
    }
}
