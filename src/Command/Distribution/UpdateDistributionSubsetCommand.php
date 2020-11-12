<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DistributionContentsDependency\DistributionContentsDependencyGroup;
use App\Entity\FAIRData\Distribution;

class UpdateDistributionSubsetCommand
{
    private Distribution $distribution;

    private ?DistributionContentsDependencyGroup $dependencies;

    public function __construct(
        Distribution $distribution,
        ?DistributionContentsDependencyGroup $dependencies
    ) {
        $this->distribution = $distribution;
        $this->dependencies = $dependencies;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getDependencies(): ?DistributionContentsDependencyGroup
    {
        return $this->dependencies;
    }
}
