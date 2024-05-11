<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Dataset;

class CreateDatasetMetadataCommand extends CreateMetadataCommand
{
    public function __construct(
        private readonly Dataset $dataset,
        VersionType $versionType,
        string $modelId,
        string $modelVersionId,
    ) {
        parent::__construct($versionType, $modelId, $modelVersionId);
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }
}
