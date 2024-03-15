<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;

class UpdateMetadataModelOptionGroupCommand extends UpdateOptionGroupCommand
{
    private MetadataModelOptionGroup $optionGroup;

    /** @param array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> $options */
    public function __construct(MetadataModelOptionGroup $optionGroup, string $title, ?string $description, array $options)
    {
        parent::__construct($title, $description, $options);

        $this->optionGroup = $optionGroup;
    }

    public function getOptionGroup(): MetadataModelOptionGroup
    {
        return $this->optionGroup;
    }
}
