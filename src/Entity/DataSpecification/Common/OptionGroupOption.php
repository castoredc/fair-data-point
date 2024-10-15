<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_dictionary_option_option')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorMap(['metadata_model' => 'App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption'])]
abstract class OptionGroupOption
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private string $value;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    protected ?int $order;

    #[ORM\JoinColumn(name: 'option_group', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: OptionGroup::class, inversedBy: 'options', cascade: ['persist'])]
    private OptionGroup $optionGroup;

    public function __construct(string $title, ?string $description, string $value, ?int $order)
    {
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
        $this->order = $order;
    }

    public function getId(): string
    {
        return (string) $this->id;
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

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }
}
