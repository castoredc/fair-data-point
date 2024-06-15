<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DistributionContents\Dependency\DependencyGroup;
use App\Entity\FAIRData\Distribution;

class UpdateDistributionSubsetCommand
{
    public function __construct(
        private Distribution $distribution,
        private ?DependencyGroup $dependencies,
    ) {
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }
}
