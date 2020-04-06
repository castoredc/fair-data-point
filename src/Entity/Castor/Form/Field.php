<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\Structure\MetadataPoint;
use App\Entity\Castor\Structure\Step\Step;

class Field
{
    /**
     * Unique identifier of the Field (same as field_id)
     *
     * @var string|null
     */
    private $id;

    /**
     * The Field type
     *
     * @var string|null
     */
    private $type;
    /**
     * The Field label
     *
     * @var string|null
     */
    private $label;

    /**
     * The Field&#39;s position within a step
     *
     * @var float|null
     */
    private $number;

    /**
     * The Field&#39;s variable name
     *
     * @var string|null
     */
    private $variableName;

    /**
     * If enabled, it makes the field required (cannot be left empty)
     *
     * @var bool|null
     */
    private $required;

    /**
     * If enabled, it hides the field in data-entry
     *
     * @var bool|null
     */
    private $hidden;

    /**
     * Information about the field. It is show during data-entry
     *
     * @var string|null
     */
    private $info;

    /**
     * The field&#39;s measurement unit
     *
     * @var string|null
     */
    private $units;

    /** @var Step */
    private $parent;

    /**
     * The field&#39;s parent id
     *
     * @var string|null
     */
    private $parentId;

    /** @var FieldOptionGroup|null */
    private $optionGroup;

    /** @var array<MetadataPoint> */
    private $metadata;

    public function __construct(?string $id, ?string $type, ?string $label, ?float $number, ?string $variableName, ?bool $required, ?bool $hidden, ?string $info, ?string $units, ?string $parentId, ?FieldOptionGroup $optionGroup)
    {
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->number = $number;
        $this->variableName = $variableName;
        $this->required = $required;
        $this->hidden = $hidden;
        $this->info = $info;
        $this->units = $units;
        $this->parentId = $parentId;
        $this->optionGroup = $optionGroup;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getNumber(): ?float
    {
        return $this->number;
    }

    public function setNumber(?float $number): void
    {
        $this->number = $number;
    }

    public function getVariableName(): ?string
    {
        return $this->variableName;
    }

    public function setVariableName(?string $variableName): void
    {
        $this->variableName = $variableName;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): void
    {
        $this->info = $info;
    }

    public function getUnits(): ?string
    {
        return $this->units;
    }

    public function setUnits(?string $units): void
    {
        $this->units = $units;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return MetadataPoint[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param MetadataPoint[] $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getOptionGroup(): ?FieldOptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(?FieldOptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    public function getParent(): Step
    {
        return $this->parent;
    }

    public function setParent(Step $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Field
    {
        return new Field(
            $data['id'] ?? null,
            $data['field_type'] ?? null,
            $data['field_label'] ?? null,
            $data['field_number'] ?? null,
            $data['field_variable_name'] ?? null,
            $data['field_required'] ?? null,
            $data['field_hidden'] ?? null,
            $data['field_info'] ?? null,
            $data['field_units'] ?? null,
            $data['parent_id'] ?? null,
            isset($data['option_group']) ? FieldOptionGroup::fromData($data['option_group']) : null
        );
    }
}
