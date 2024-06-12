<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;

class MetadataModelDisplaySettingApiResource
{
    public function __construct(private MetadataModelDisplaySetting $displaySetting)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->displaySetting->getId(),
            'title' => $this->displaySetting->getTitle(),
            'order' => $this->displaySetting->getOrder(),
            'node' => $this->displaySetting->getNode()->getId(),
            'resourceType' => $this->displaySetting->getResourceType()->toString(),
            'type' => $this->displaySetting->getDisplayType()->toString(),
            'position' => $this->displaySetting->getDisplayPosition()->toString(),
        ];
    }
}
