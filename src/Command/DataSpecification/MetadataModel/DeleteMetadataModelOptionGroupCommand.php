<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;

class DeleteMetadataModelOptionGroupCommand
{
    public function __construct(private MetadataModelOptionGroup $optionGroup)
    {
    }

    public function getOptionGroup(): MetadataModelOptionGroup
    {
        return $this->optionGroup;
    }
}
