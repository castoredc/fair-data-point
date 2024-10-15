<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelOptionGroupCommand extends CreateOptionGroupCommand
{
    /** @param array<array{title: string, description: string|null, value: string, order: int|null}> $options */
    public function __construct(private MetadataModelVersion $metadataModelVersion, string $title, array $options, ?string $description)
    {
        parent::__construct($title, $options, $description);
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
