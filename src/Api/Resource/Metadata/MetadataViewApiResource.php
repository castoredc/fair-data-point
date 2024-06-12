<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Metadata\Metadata;
use App\Service\MetadataDisplayHelper;

class MetadataViewApiResource implements ApiResource
{
    public function __construct(private Metadata $metadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $displaySettings = $this->metadata->getMetadataModelVersion()->getDisplaySettingsForResourceType($this->metadata->getResourceType());

        $return = [
            MetadataDisplayPosition::TITLE => [],
            MetadataDisplayPosition::DESCRIPTION => [],
            MetadataDisplayPosition::SIDEBAR => [],
            MetadataDisplayPosition::MODAL => [],
        ];

        foreach ($displaySettings as $displaySetting) {
            $return[$displaySetting->getDisplayPosition()->toString()][] = [
                'title' => $displaySetting->getTitle(),
                'order' => $displaySetting->getOrder(),
                'type' => $displaySetting->getDisplayType()->toString(),
                'dataType' => $displaySetting->getNode()->getDataType()?->toString(),
                'value' => MetadataDisplayHelper::getValueForDisplay(
                    $this->metadata,
                    $displaySetting
                ),
            ];
        }

        return $return;
    }
}
