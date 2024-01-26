<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateModelModuleCommand;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class CreateMetadataModelModuleCommand extends CreateModelModuleCommand
{
    private MetadataModelVersion $metadataModelVersion;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DependencyGroup $dependencies = null;

    public function __construct(MetadataModelVersion $metadataModelVersion, string $title, int $order, bool $isRepeated, bool $isDependent, ?DependencyGroup $dependencies)
    {
        parent::__construct($title, $order);

        $this->metadataModelVersion = $metadataModelVersion;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function isDependent(): bool
    {
        return $this->isDependent;
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }
}
