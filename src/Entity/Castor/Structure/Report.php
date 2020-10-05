<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\StructureType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Report extends StructureElement
{
    private ?string $description = null;

    private ?string $type = null;

    public function __construct(string $id, CastorStudy $study, ?string $name, ?string $description, ?string $type)
    {
        parent::__construct($id, $study, StructureType::report(), $name);

        $this->description = $description;
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, CastorStudy $study): Report
    {
        return new Report(
            $data['id'],
            $study,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['type'] ?? null
        );
    }
}
