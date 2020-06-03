<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Study;
use App\Entity\Enum\StructureType;
use Doctrine\ORM\Mapping as ORM;
use function ksort;

/**
 * @ORM\Entity
 */
abstract class Step extends CastorEntity
{
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

    public function __construct(string $id, Study $study, StructureType $structureType, ?string $description, string $name, ?int $position)
    {
        parent::__construct($id, $name, $study, $structureType);

        $this->description = $description;
        $this->name = $name;
        $this->position = $position;
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
