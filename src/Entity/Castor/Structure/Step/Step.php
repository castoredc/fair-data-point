<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Structure\StructureElement;
use function ksort;
use function uasort;

abstract class Step
{
    /**
     * Unique identifier of the Step (same as step_id)
     *
     * @var string|null
     */
    private $id;

    /**
     * the Step description
     *
     * @var string|null
     */
    private $description;

    /**
     * the name of the Step
     *
     * @var string|null
     */
    private $name;

    /**
     * The Steps&#39;s order within the study
     *
     * @var int|null
     */
    private $position;

    /** @var Field[] */
    protected $fields;

    /** @var StructureElement */
    protected $parent;

    public function __construct(?string $id, ?string $description, ?string $name, ?int $position)
    {
        $this->id = $id;
        $this->description = $description;
        $this->name = $name;
        $this->position = $position;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getParent(): StructureElement
    {
        return $this->parent;
    }

    public function setParent(StructureElement $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        $return = [];

        foreach ($this->fields as $field) {
            $return[$field->getNumber()] = $field;
        }

        ksort($return);

        return $return;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setField(string $fieldId, Field $field): void
    {
        $this->fields[$fieldId] = $field;
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->getNumber()] = $field;
    }
}
