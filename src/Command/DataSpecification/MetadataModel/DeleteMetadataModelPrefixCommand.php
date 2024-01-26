<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;

class DeleteMetadataModelPrefixCommand
{
    private NamespacePrefix $metadataModelPrefix;

    public function __construct(NamespacePrefix $metadataModelPrefix)
    {
        $this->metadataModelPrefix = $metadataModelPrefix;
    }

    public function getMetadataModelPrefix(): NamespacePrefix
    {
        return $this->metadataModelPrefix;
    }
}
