<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\DataModelMappingType;
use App\Entity\Enum\StructureType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use function boolval;

/**
 * @Assert\GroupSequenceProvider()
 */
class DataModelMappingApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $type;

    /**
     * @Assert\NotBlank(groups = {"node"})
     * @Assert\Type("string")
     */
    private ?string $node;

    /** @Assert\Type("bool", groups = {"node"}) */
    private bool $transform;

    /**
     * @Assert\NotBlank(groups = {"transform"})
     * @Assert\Type("string")
     */
    private ?string $transformSyntax;

    /**
     * @Assert\NotBlank(groups = {"node"})
     * @var string[]
     */
    private ?array $elements;

    /** @Assert\NotBlank(groups = {"module"}) */
    private ?string $structureType;

    /**
     * @Assert\NotBlank(groups = {"module"})
     * @Assert\Type("string")
     */
    private ?string $module;

    /**
     * @Assert\NotBlank(groups = {"module"})
     * @Assert\Type("string")
     */
    private ?string $element;

    protected function parse(): void
    {
        $this->type = (string) $this->getFromData('type');

        // Node
        $this->elements = $this->getFromData('elements');
        $this->node = $this->getFromData('node');
        $this->transform = boolval($this->getFromData('transform'));
        $this->transformSyntax = $this->getFromData('transformSyntax');

        // Module
        $this->element = $this->getFromData('element');
        $this->structureType = $this->getFromData('structureType');
        $this->module = $this->getFromData('module');
    }

    public function getType(): DataModelMappingType
    {
        return DataModelMappingType::fromString($this->type);
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getStructureType(): StructureType
    {
        return StructureType::fromString($this->structureType);
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getTransform(): bool
    {
        return $this->transform;
    }

    public function getTransformSyntax(): ?string
    {
        return $this->transformSyntax;
    }

    public function getElement(): ?string
    {
        return $this->element;
    }

    /**
     * @return string[]|null
     */
    public function getElements(): ?array
    {
        return $this->elements;
    }

    /** @inheritDoc */
    public function getGroupSequence()
    {
        $groups = [
            'DataModelMappingApiRequest',
            $this->getType()->toString(),
        ];

        if ($this->getTransform() && $this->getType()->isNode()) {
            $groups[] = 'transform';
        }

        return $groups;
    }
}
