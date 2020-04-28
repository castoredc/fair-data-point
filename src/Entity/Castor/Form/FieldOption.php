<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Study;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FieldOption extends CastorEntity
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $value;

    /** @var int|null */
    private $groupOrder;

    /** @var FieldOptionGroup */
    private $optionGroup;

    public function __construct(string $id, Study $study, ?string $name, ?string $value, ?int $groupOrder)
    {
        parent::__construct($id, $study, null);

        $this->name = $name;
        $this->value = $value;
        $this->groupOrder = $groupOrder;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getGroupOrder(): ?int
    {
        return $this->groupOrder;
    }

    public function setGroupOrder(?int $groupOrder): void
    {
        $this->groupOrder = $groupOrder;
    }

    public function getOptionGroup(): FieldOptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(FieldOptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, Study $study): FieldOption
    {
        return new FieldOption(
            $data['id'],
            $study,
            $data['name'] ?? null,
            $data['value'] ?? null,
            $data['groupOrder'] ?? null
        );
    }
}
