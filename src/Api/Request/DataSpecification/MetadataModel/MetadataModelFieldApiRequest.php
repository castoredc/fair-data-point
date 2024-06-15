<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\MetadataFieldType;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class MetadataModelFieldApiRequest extends SingleApiRequest
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

    /** @Assert\Type("string") */
    private ?string $description = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $node;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $fieldType;

    /** @Assert\Type("string") */
    private ?string $optionGroup = null;

    /**
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private bool $isRequired;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
        $this->description = $this->getFromData('description');
        $this->node = $this->getFromData('node');
        $this->fieldType = $this->getFromData('fieldType');
        $this->optionGroup = $this->getFromData('optionGroup');
        $this->isRequired = boolval($this->getFromData('isRequired'));
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getFieldType(): ?MetadataFieldType
    {
        return MetadataFieldType::fromString($this->fieldType);
    }

    public function getOptionGroup(): ?string
    {
        return $this->optionGroup;
    }

    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }
}
