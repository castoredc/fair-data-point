<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Castor\Study;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedText;
use App\Security\CastorUser;
use DateTimeImmutable;

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
