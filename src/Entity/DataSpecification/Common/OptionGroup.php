<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Element;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_dictionary_option_group')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['metadata_model' => 'App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup'])]
abstract class OptionGroup
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\JoinColumn(name: 'version', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Version::class, inversedBy: 'optionGroups', cascade: ['persist'])]
    private Version $version;

    /** @var Collection<OptionGroupOption> */
    #[ORM\OneToMany(targetEntity: \OptionGroupOption::class, mappedBy: 'optionGroup', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['order' => 'ASC'])]
    private Collection $options;

    /** @var Collection<Element> */
    #[ORM\OneToMany(targetEntity: Element::class, mappedBy: 'optionGroup')]
    private Collection $elements;

    public function __construct(Version $version, string $title, ?string $description)
    {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
        $this->options = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
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

    /** @return Collection<OptionGroupOption> */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /** @return array<string, OptionGroupOption> */
    public function getOptionsWithId(): array
    {
        $options = [];

        foreach ($this->options as $option) {
            $options[$option->getId()] = $option;
        }

        return $options;
    }

    /** @param Collection<OptionGroupOption> $options */
    public function setOptions(Collection $options): void
    {
        $this->options = $options;
    }

    public function addOption(OptionGroupOption $option): void
    {
        $option->setOptionGroup($this);
        $this->options->add($option);
    }

    public function removeOption(OptionGroupOption $option): void
    {
        $this->options->removeElement($option);
    }

    /** @return Collection<Element> */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function getOption(string $value): ?OptionGroupOption
    {
        return $this->options->findFirst(static function (int $key, OptionGroupOption $option) use ($value) {
            return $option->getValue() === $value;
        });
    }
}
