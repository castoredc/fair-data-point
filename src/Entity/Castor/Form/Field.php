<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Structure\MetadataPoint;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use function boolval;
use function in_array;

/**
 * @ORM\Entity
 */
class Field extends CastorEntity
{
    public const EXPORTABLE = [
        'numeric',
        'radio',
        'dropdown',
        'checkbox',
        'date',
        'year',
        'time',
        'calculation',
        'slider',
        'string',
        'textarea',
        'randomization',
        'grid',
        'datetime',
        'numberdate',
    ];

    public const EXPORTABLE_ANNOTATED = [
        'radio',
        'dropdown',
        'checkbox',
    ];

    public const EXPORTABLE_PLAIN = [
        'numeric',
        'radio',
        'dropdown',
        'checkbox',
        'date',
        'year',
        'time',
        'calculation',
        'slider',
        'string',
        'textarea',
        'randomization',
        'grid',
        'datetime',
        'numberdate',
    ];

    public const SUPPORTED_DATA_TYPES = [
        'numeric' => [XsdDataType::NUMBER_TYPES],
        'radio' => [XsdDataType::NUMBER_TYPES, XsdDataType::STRING_TYPES, XsdDataType::BOOLEAN_TYPES],
        'dropdown' => [XsdDataType::NUMBER_TYPES, XsdDataType::STRING_TYPES, XsdDataType::BOOLEAN_TYPES],
        'checkbox' => [XsdDataType::NUMBER_TYPES, XsdDataType::STRING_TYPES, XsdDataType::BOOLEAN_TYPES],
        'date' => [XsdDataType::DATE_TIME_TYPES],
        'year' => [XsdDataType::G_YEAR],
        'time' => [XsdDataType::TIME],
        'calculation' => [XsdDataType::ANY_TYPES],
        'slider' => [XsdDataType::NUMBER_TYPES],
        'string' => [XsdDataType::STRING_TYPES],
        'textarea' => [XsdDataType::STRING_TYPES],
        'randomization' => [XsdDataType::NUMBER_TYPES, XsdDataType::STRING_TYPES],
        'grid' => [XsdDataType::STRING_TYPES],
        'datetime' => [XsdDataType::DATE_TIME_TYPES],
        'numberdate' => [XsdDataType::NUMBER_TYPES, XsdDataType::DATE_TYPES],
    ];

    /**
     * The Field type
     */
    private ?string $type = null;
    /**
     * The Field label
     */
    private ?string $fieldLabel = null;

    /**
     * The Field&#39;s position within a step
     */
    private ?float $number = null;

    /**
     * The Field&#39;s variable name
     */
    private ?string $variableName = null;

    /**
     * If enabled, it makes the field required (cannot be left empty)
     */
    private ?bool $required = null;

    /**
     * If enabled, it hides the field in data-entry
     */
    private ?bool $hidden = null;

    /**
     * Information about the field. It is show during data-entry
     */
    private ?string $info = null;

    /**
     * The field&#39;s measurement unit
     */
    private ?string $units = null;

    /**
     * The field&#39;s parent id
     */
    private ?string $parentId = null;

    private ?FieldOptionGroup $optionGroup = null;

    private ?string $optionGroupId = null;

    /** @var array<MetadataPoint> */
    private array $metadata;

    public function __construct(string $id, CastorStudy $study, ?string $type, string $label, ?float $number, ?string $variableName, ?bool $required, ?bool $hidden, ?string $info, ?string $units, ?string $parentId, ?string $optionGroupId)
    {
        parent::__construct($id, $label, $study, null);

        $this->type = $type;
        $this->fieldLabel = $label;
        $this->number = $number;
        $this->variableName = $variableName;
        $this->required = $required;
        $this->hidden = $hidden;
        $this->info = $info;
        $this->units = $units;
        $this->parentId = $parentId;
        $this->optionGroupId = $optionGroupId;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getFieldLabel(): string
    {
        return $this->fieldLabel;
    }

    public function setFieldLabel(?string $fieldLabel): void
    {
        $this->fieldLabel = $fieldLabel;
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

    public function getOptionGroupId(): ?string
    {
        return $this->optionGroupId;
    }

    public function isExportable(): bool
    {
        return in_array($this->type, self::EXPORTABLE, true);
    }

    public function isExportableAnnotated(): bool
    {
        return in_array($this->type, self::EXPORTABLE_ANNOTATED, true);
    }

    public function isExportablePlain(): bool
    {
        return in_array($this->type, self::EXPORTABLE_PLAIN, true);
    }

    /** @return string[] */
    public function getSupportedDataTypes(): array
    {
        if (! $this->isExportable()) {
            return [];
        }

        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator(self::SUPPORTED_DATA_TYPES[$this->type]));
        $types = [];

        foreach ($it as $type) {
            $types[] = $type;
        }

        return $types;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, CastorStudy $study): Field
    {
        return new Field(
            $data['id'] ?? null,
            $study,
            $data['field_type'] ?? null,
            $data['field_label'],
            $data['field_number'] ?? null,
            $data['field_variable_name'] ?? null,
            boolval($data['field_required']) ?? null,
            boolval($data['field_hidden']) ?? null,
            $data['field_info'] ?? null,
            $data['field_units'] ?? null,
            $data['parent_id'] ?? null,
            isset($data['option_group']) ? $data['option_group']['id'] : null,
        );
    }
}
