<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\Study;

class CreateStudyMetadataCommand extends CreateMetadataCommand
{
    public function __construct(
        private readonly Study $study,
        VersionType $versionType,
        string $modelId,
        string $modelVersionId,
    ) {
        parent::__construct($versionType, $modelId, $modelVersionId);
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}
