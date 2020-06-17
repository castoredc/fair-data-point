<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Structure\MetadataPoint;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;
use function boolval;

/**
 * @ORM\Entity
 */
class Field extends CastorEntity
{
    const EXPORTABLE = [
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

    const EXPORTABLE_ANNOTATED = [
        'radio',
        'dropdown',
        'checkbox',
    ];

    const EXPORTABLE_PLAIN = [
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

    const SUPPORTED_DATA_TYPES = [
        'numeric' => XsdDataType::NUMBER_TYPES,
        'radio' => XsdDataType::NUMBER_TYPES + XsdDataType::STRING_TYPES,
        'dropdown' => XsdDataType::NUMBER_TYPES + XsdDataType::STRING_TYPES,
        'checkbox' => XsdDataType::NUMBER_TYPES + XsdDataType::STRING_TYPES,
        'date' => XsdDataType::DATE_TIME_TYPES,
        'year' => [XsdDataType::G_YEAR],
        'time' => [XsdDataType::TIME],
        'calculation' => XsdDataType::ANY_TYPES,
        'slider' => XsdDataType::NUMBER_TYPES,
        'string' => XsdDataType::STRING_TYPES,
        'textarea' => XsdDataType::STRING_TYPES,
        'randomization' => XsdDataType::NUMBER_TYPES + XsdDataType::STRING_TYPES,
        'grid' => XsdDataType::STRING_TYPES,
        'datetime' => XsdDataType::DATE_TIME_TYPES,
        'numberdate' => XsdDataType::NUMBER_TYPES + XsdDataType::DATE_TYPES,
    ];

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
    private $fieldLabel;

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

    public function __construct(string $id, CastorStudy $study, ?string $type, string $label, ?float $number, ?string $variableName, ?bool $required, ?bool $hidden, ?string $info, ?string $units, ?string $parentId, ?FieldOptionGroup $optionGroup)
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
        $this->optionGroup = $optionGroup;
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

    public function isExportable(): bool
    {
        return in_array($this->type, self::EXPORTABLE);
    }

    public function isExportableAnnotated(): bool
    {
        return in_array($this->type, self::EXPORTABLE_ANNOTATED);
    }

    public function isExportablePlain(): bool
    {
        return in_array($this->type, self::EXPORTABLE_PLAIN);
    }

    /** @return string[] */
    public function getSupportedDataTypes(): array
    {
        if(! $this->isExportable()) {
            return [];
        }

        return self::SUPPORTED_DATA_TYPES[$this->type];
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
            isset($data['option_group']) ? FieldOptionGroup::fromData($data['option_group'], $study) : null
        );
    }
}
