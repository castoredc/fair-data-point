<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelOptionGroupCommand extends CreateOptionGroupCommand
{
    private MetadataModelVersion $metadataModelVersion;

    /** @param array<array{title: string, description: string|null, value: string, order: int|null}> $options */
    public function __construct(MetadataModelVersion $metadataModelVersion, string $title, ?string $description, array $options)
    {
        parent::__construct($title, $description, $options);

        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }
}
