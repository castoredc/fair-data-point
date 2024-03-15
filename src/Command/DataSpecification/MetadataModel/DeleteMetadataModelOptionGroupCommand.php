<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;

class DeleteMetadataModelOptionGroupCommand
{
    private MetadataModelOptionGroup $optionGroup;

    public function __construct(MetadataModelOptionGroup $optionGroup)
    {
        $this->optionGroup = $optionGroup;
    }

    public function getOptionGroup(): MetadataModelOptionGroup
    {
        return $this->optionGroup;
    }
}
