<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\MetadataDisplayType;
use App\Entity\Enum\ResourceType;
use Doctrine\Common\Collections\ArrayCollection;

class DisplaySettingFactory
{
    /**
     * @param array<mixed>          $data
     * @param ArrayCollection<Node> $nodes
     */
    public function createFromJson(MetadataModelVersion $version, array $data, ArrayCollection $nodes): MetadataModelDisplaySetting
    {
        return new MetadataModelDisplaySetting(
            $data['title'],
            $data['order'],
            $nodes->get($data['node']),
            MetadataDisplayType::fromString($data['type']),
            MetadataDisplayPosition::fromString($data['position']),
            ResourceType::fromString($data['resourceType']),
            $version
        );
    }
}
