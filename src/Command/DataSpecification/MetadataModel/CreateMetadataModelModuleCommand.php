<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelModuleCommand;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelModuleCommand extends CreateModelModuleCommand
{
    private MetadataModelVersion $metadataModelVersion;

    public function __construct(MetadataModelVersion $metadataModelVersion, string $title, int $order)
    {
        parent::__construct($title, $order);

        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
