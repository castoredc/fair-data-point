<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelVersionCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\VersionType;

class CreateMetadataModelVersionCommand extends CreateModelVersionCommand
{
    private MetadataModel $metadataModel;

    public function __construct(MetadataModel $metadataModel, VersionType $versionType)
    {
        parent::__construct($versionType);

        $this->metadataModel = $metadataModel;
    }

    public function getMetadataModel(): MetadataModel
    {
        return $this->metadataModel;
    }
}
