<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\ResourceType;

class MetadataModelDisplaySettingsApiResource implements ApiResource
{
    private const DEFAULT_RESOURCE_DATA = [
        MetadataDisplayPosition::TITLE => [],
        MetadataDisplayPosition::DESCRIPTION => [],
        MetadataDisplayPosition::SIDEBAR => [],
        MetadataDisplayPosition::MODAL => [],
    ];

    public function __construct(private MetadataModelVersion $metadataModel)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            ResourceType::FDP => self::DEFAULT_RESOURCE_DATA,
            ResourceType::CATALOG => self::DEFAULT_RESOURCE_DATA,
            ResourceType::DATASET => self::DEFAULT_RESOURCE_DATA,
            ResourceType::DISTRIBUTION => self::DEFAULT_RESOURCE_DATA,
            ResourceType::STUDY => self::DEFAULT_RESOURCE_DATA,
        ];

        foreach ($this->metadataModel->getDisplaySettings() as $displaySetting) {
            $data[$displaySetting->getResourceType()->toString()][$displaySetting->getDisplayPosition()->toString()][] = (new MetadataModelDisplaySettingApiResource($displaySetting))->toArray();
        }

        return $data;
    }
}
