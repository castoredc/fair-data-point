<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;

class DeleteMetadataModelDisplaySettingCommand
{
    public function __construct(private MetadataModelDisplaySetting $displaySetting)
    {
    }

    public function getDisplaySetting(): MetadataModelDisplaySetting
    {
        return $this->displaySetting;
    }
}
