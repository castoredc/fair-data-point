<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Distribution;

class CreateDistributionMetadataCommand extends CreateMetadataCommand
{
    public function __construct(
        private readonly Distribution $distribution,
        VersionType $versionType,
        string $modelId,
        string $modelVersionId,
    ) {
        parent::__construct($versionType, $modelId, $modelVersionId);
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
