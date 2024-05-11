<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;

abstract class CreateMetadataCommand
{
    public function __construct(
        private VersionType $versionType,
        private string $modelId,
        private string $modelVersionId,
    ) {
    }

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }

    public function getModelId(): string
    {
        return $this->modelId;
    }

    public function getModelVersionId(): string
    {
        return $this->modelVersionId;
    }
}
