<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Entity\Data\DistributionContentsDependency\DistributionContentsDependencyGroup;
use Symfony\Component\Validator\Constraints as Assert;
use function count;

class DistributionSubsetApiRequest extends SingleApiRequest
{
    /**
     * @var mixed[]|null
     * @Assert\NotBlank()
     */
    private ?array $dependencies = null;

    protected function parse(): void
    {
        $this->dependencies = $this->getFromData('dependencies');
    }

    public function getDependencies(): ?DistributionContentsDependencyGroup
    {
        return $this->generateDependencies();
    }

    private function generateDependencies(): ?DistributionContentsDependencyGroup
    {
        if ($this->dependencies === null || count($this->dependencies['rules']) === 0) {
            return null;
        }

        return DistributionContentsDependencyGroup::fromData($this->dependencies);
    }
}
