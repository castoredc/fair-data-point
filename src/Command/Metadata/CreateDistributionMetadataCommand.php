<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedText;

class CreateDistributionMetadataCommand extends CreateMetadataCommand
{
    /** @param Agent[] $publishers */
    public function __construct(
        private Distribution $distribution,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers,
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
