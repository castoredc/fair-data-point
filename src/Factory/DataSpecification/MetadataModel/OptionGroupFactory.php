<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class OptionGroupFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(MetadataModelVersion $version, array $data): MetadataModelOptionGroup
    {
        $optionGroup = new MetadataModelOptionGroup($version, $data['title'], $data['description']);

        foreach ($data['options'] as $option) {
            $option = new MetadataModelOptionGroupOption(
                $option['title'],
                $option['description'] ?? null,
                $option['value'],
                $option['order'] ?? null
            );

            $optionGroup->addOption($option);
        }

        return $optionGroup;
    }
}
