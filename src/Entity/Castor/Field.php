<?php
declare(strict_types=1);

namespace App\Entity\Castor;

class Field
{
    /** @var string */
    private $id;

    /** @var string */
    private $type;

    /** @var string|null */
    private $label;

    /** @var string|null */
    private $variableName;

    /** @var array<MetadataPoint> */
    private $metadata;

    public function __construct(string $id, string $type, ?string $label, ?string $variableName)
    {
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->variableName = $variableName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getVariableName(): ?string
    {
        return $this->variableName;
    }

    /**
     * @return array<MetadataPoint>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<MetadataPoint> $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Field
    {
        $field = new Field(
            $data['field_id'],
            $data['field_type'],
            $data['field_label'] ?? null,
            $data['field_variable_name'] ?? null,
        );

        $metadata = [];

        if ($data['metadata_points'] !== null) {
            foreach ($data['metadata_points'] as $metadata_point) {
                $metadata[] = MetadataPoint::fromData($metadata_point);
            }

            $field->setMetadata($metadata);
        }

        return $field;
    }
}
