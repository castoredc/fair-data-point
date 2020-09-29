<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\DataModelMappingType;
use App\Entity\Enum\StructureType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

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
    private string $node;

    /**
     * @Assert\NotBlank(groups = {"module"})
     * @Assert\Type("string")
     */
    private string $structureType;

    /**
     * @Assert\NotBlank(groups = {"module"})
     * @Assert\Type("string")
     */
    private string $module;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $element;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
        $this->node = $this->getFromData('node');
        $this->structureType = $this->getFromData('structureType');
        $this->module = $this->getFromData('module');
        $this->element = $this->getFromData('element');
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

    public function getElement(): string
    {
        return $this->element;
    }

    /** @inheritDoc */
    public function getGroupSequence()
    {
        return [
            'DataModelMappingApiRequest',
            $this->getType()->toString(),
        ];
    }
}
