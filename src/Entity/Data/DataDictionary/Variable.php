<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Enum\DataDictionaryDataType;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_variable")
 * @ORM\HasLifecycleCallbacks
 */
class Variable
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionaryGroup", inversedBy="variables", cascade={"persist"})
     * @ORM\JoinColumn(name="group", referencedColumnName="id", nullable=false)
     */
    private DataDictionaryGroup $group;

    /** @ORM\Column(type="string") */
    private string $label;

    /** @ORM\Column(type="string") */
    private string $name;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $description = null;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $format = null;

    /** @ORM\Column(type="DataDictionaryDataType") */
    private DataDictionaryDataType $dataType;

    /** @ORM\Column(name="`order`", type="integer") */
    private int $order;

    /**
     * @ORM\ManyToOne(targetEntity="OptionGroup", inversedBy="distributions")
     * @ORM\JoinColumn(name="option_group", referencedColumnName="id", nullable=true)
     */
    private ?OptionGroup $optionGroup;

    public function __construct(DataDictionaryGroup $group, int $order, string $label, string $name, ?string $description, DataDictionaryDataType $dataType)
    {
        $this->group = $group;
        $this->order = $order;
        $this->label = $label;
        $this->name = $name;
        $this->description = $description;
        $this->dataType = $dataType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): DataDictionaryGroup
    {
        return $this->group;
    }

    public function setGroup(DataDictionaryGroup $group): void
    {
        $this->group = $group;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDataType(): DataDictionaryDataType
    {
        return $this->dataType;
    }

    public function setDataType(DataDictionaryDataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
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
}
