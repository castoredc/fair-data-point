<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedText;

class CreateDistributionMetadataCommand extends CreateMetadataCommand
{
    private Distribution $distribution;

    /** @param Agent[] $publishers */
    public function __construct(
        Distribution $distribution,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);

        $this->distribution = $distribution;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
