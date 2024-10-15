<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\DataModel;

use App\Api\Request\DataSpecification\Common\DataSpecificationModuleApiRequest;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use Symfony\Component\Validator\Constraints as Assert;
use function count;

class DataModelModuleApiRequest extends DataSpecificationModuleApiRequest
{
    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $repeated;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $dependent;

    /**
     * @var mixed[]|null
     */
    #[Assert\NotBlank(groups: ['dependent'])]
    private ?array $dependencies = null;

    protected function parse(): void
    {
        parent::parse();

        $this->repeated = $this->getFromData('repeated');
        $this->dependent = $this->getFromData('dependent');
        $this->dependencies = $this->getFromData('dependencies');
    }

    public function isRepeated(): bool
    {
        return $this->repeated;
    }

    public function isDependent(): bool
    {
        return $this->dependent;
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

    public function getGroupSequence(): array|Assert\GroupSequence
    {
        $sequence = ['DataModelModuleApiRequest'];

        if ($this->isDependent()) {
            $sequence[] = 'dependent';
        }

        return $sequence;
    }
}
