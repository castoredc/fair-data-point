<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FieldOption extends CastorEntity
{
    private ?string $name = null;

    private FieldOptionGroup $optionGroup;

    public function __construct(string $id, CastorStudy $study, string $name, private ?string $value = null, private ?int $groupOrder = null)
    {
        parent::__construct($id, $name, $study, null);

        $this->name = $name;
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
        $this->setParent($optionGroup);
        $this->optionGroup = $optionGroup;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data, CastorStudy $study): FieldOption
    {
        return new FieldOption(
            $data['id'],
            $study,
            $data['name'],
            $data['value'] ?? null,
            $data['groupOrder'] ?? null
        );
    }
}
