<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedText;

class CreateDistributionMetadataCommand extends CreateMetadataCommand
{
    /** @var Distribution */
    private $distribution;

    public function __construct(
        Distribution $distribution,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate);

        $this->distribution = $distribution;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
