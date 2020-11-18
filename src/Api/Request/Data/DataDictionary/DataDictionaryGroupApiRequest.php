<?php
declare(strict_types=1);

namespace App\Api\Request\Data\DataDictionary;

use App\Api\Request\SingleApiRequest;
use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use function count;

class DataDictionaryGroupApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $title;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private int $order;

    /**
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private bool $repeated;

    /**
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private bool $dependent;

    /**
     * @var mixed[]|null
     * @Assert\NotBlank(groups = {"dependent"})
     */
    private ?array $dependencies = null;

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

    /** @inheritDoc */
    public function getGroupSequence()
    {
        $sequence = ['DataDictionaryGroupApiRequest'];

        if ($this->isDependent()) {
            $sequence[] = 'dependent';
        }

        return $sequence;
    }
}
