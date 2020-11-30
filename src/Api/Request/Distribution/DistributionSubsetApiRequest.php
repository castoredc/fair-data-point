<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Entity\Data\DistributionContents\Dependency\DependencyGroup;
use function count;

class DistributionSubsetApiRequest extends SingleApiRequest
{
    /** @var mixed[]|null */
    private ?array $dependencies = null;

    protected function parse(): void
    {
        $this->dependencies = $this->getFromData('dependencies');
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->generateDependencies();
    }

    private function generateDependencies(): ?DependencyGroup
    {
        if ($this->dependencies === null || count($this->dependencies['rules']) === 0) {
            return null;
        }

        return DependencyGroup::fromData($this->dependencies);
    }
}
