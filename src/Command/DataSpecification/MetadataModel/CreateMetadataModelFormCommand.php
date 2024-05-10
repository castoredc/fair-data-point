<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelFormCommand
{
    private MetadataModelVersion $metadataModelVersion;

    private string $title;

    private int $order;

    public function __construct(MetadataModelVersion $metadataModelVersion, string $title, int $order)
    {
        $this->title = $title;
        $this->order = $order;

        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
