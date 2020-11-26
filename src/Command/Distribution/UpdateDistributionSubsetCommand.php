<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Data\DistributionContents\Dependency\DependencyGroup;
use App\Entity\FAIRData\Distribution;

class UpdateDistributionSubsetCommand
{
    private Distribution $distribution;

    private ?DependencyGroup $dependencies;

    public function __construct(
        Distribution $distribution,
        ?DependencyGroup $dependencies
    ) {
        $this->distribution = $distribution;
        $this->dependencies = $dependencies;
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
