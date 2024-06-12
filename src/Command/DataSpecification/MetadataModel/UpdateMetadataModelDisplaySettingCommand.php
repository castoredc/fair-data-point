<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\MetadataDisplayType;

class UpdateMetadataModelDisplaySettingCommand
{
    public function __construct(
        private MetadataModelDisplaySetting $displaySetting,
        private string $title,
        private int $order,
        private string $node,
        private MetadataDisplayType $displayType,
        private MetadataDisplayPosition $displayPosition,
    ) {
    }

    public function getDisplaySetting(): MetadataModelDisplaySetting
    {
        return $this->displaySetting;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getDisplayType(): MetadataDisplayType
    {
        return $this->displayType;
    }

    public function getDisplayPosition(): MetadataDisplayPosition
    {
        return $this->displayPosition;
    }
}
