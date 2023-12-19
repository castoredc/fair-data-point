<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_option_option")
 * @ORM\HasLifecycleCallbacks
 */
class OptionGroupOption
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $description = null;

    /** @ORM\Column(type="text") */
    private string $value;

    /**
     * @ORM\ManyToOne(targetEntity="OptionGroup", inversedBy="options", cascade={"persist"})
     * @ORM\JoinColumn(name="option_group", referencedColumnName="id", nullable=false)
     */
    private OptionGroup $optionGroup;

    public function __construct(string $id, string $title, ?string $description, string $value)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getOptionGroup(): OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(OptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }
}
