<?php
declare(strict_types=1);

namespace App\Api\Request\Data;

use App\Api\Request\SingleApiRequest;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use function count;

class DataModelModuleApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private $order;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private $repeated;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private $dependent;

    /**
     * @var mixed[]|null
     * @Assert\NotBlank(groups = {"dependent"})
     */
    private $dependencies;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
        $this->repeated = $this->getFromData('repeated');
        $this->dependent = $this->getFromData('dependent');
        $this->dependencies = $this->getFromData('dependencies');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function isRepeated(): bool
    {
        return $this->repeated;
    }

    public function isDependent(): bool
    {
        return $this->dependent;
    }

    public function getDependencies(): ?DataModelDependencyGroup
    {
        return $this->generateDependencies();
    }

    private function generateDependencies(): ?DataModelDependencyGroup
    {
        if ($this->dependencies === null || count($this->dependencies['rules']) === 0) {
            return null;
        }

        return DataModelDependencyGroup::fromData($this->dependencies);
    }

    /** @inheritDoc */
    public function getGroupSequence()
    {
        $sequence = ['DataModelModuleApiRequest'];

        if ($this->isDependent()) {
            $sequence[] = 'dependent';
        }

        return $sequence;
    }
}
