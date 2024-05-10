<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelModuleCommand extends CreateModelModuleCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion, string $title, int $order)
    {
        parent::__construct($title, $order);
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
