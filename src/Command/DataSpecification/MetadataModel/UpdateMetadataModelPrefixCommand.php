<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelPrefixCommand;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;

class UpdateMetadataModelPrefixCommand extends UpdateModelPrefixCommand
{
    private NamespacePrefix $metadataModelPrefix;

    public function __construct(NamespacePrefix $metadataModelPrefix, string $prefix, string $uri)
    {
        parent::__construct($prefix, $uri);

        $this->metadataModelPrefix = $metadataModelPrefix;
    }

    public function getMetadataModelPrefix(): NamespacePrefix
    {
        return $this->metadataModelPrefix;
    }
}
