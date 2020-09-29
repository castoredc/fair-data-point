<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

class MetadataPoint
{
    private string $id;

    private string $value;

    private ?string $description = null;

    public function __construct(string $id, string $value, ?string $description)
    {
        $this->id = $id;
        $this->value = $value;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): MetadataPoint
    {
        return new MetadataPoint(
            $data['id'],
            $data['value'],
            $data['description']
        );
    }
}
