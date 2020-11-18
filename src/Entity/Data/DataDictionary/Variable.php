<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Data\DataSpecification\Element;
use App\Entity\Data\DataSpecification\Group;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Enum\DataDictionaryDataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_variable")
 * @ORM\HasLifecycleCallbacks
 */
class Variable extends Element
{
    /** @ORM\Column(type="string") */
    private string $name;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $format = null;

    /** @ORM\Column(type="DataDictionaryDataType") */
    private DataDictionaryDataType $dataType;

    /**
     * @ORM\ManyToOne(targetEntity="OptionGroup", inversedBy="variables")
     * @ORM\JoinColumn(name="option_group", referencedColumnName="id", nullable=true)
     */
    private ?OptionGroup $optionGroup;

    public function __construct(Version $version, string $title, ?string $description, string $name, Group $group, DataDictionaryDataType $dataType, int $order)
    {
        parent::__construct($version, $title, $description);

        $this->name = $name;
        $this->group = $group;
        $this->dataType = $dataType;
        $this->order = $order;
    }

    public function getDataType(): DataDictionaryDataType
    {
        return $this->dataType;
    }

    public function setDataType(DataDictionaryDataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getOptionGroup(): ?OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(?OptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
