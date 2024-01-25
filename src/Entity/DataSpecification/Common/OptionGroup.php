<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_option_group")
 * @ORM\HasLifecycleCallbacks
 */
class OptionGroup
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $description = null;

    /**
     * @ORM\OneToMany(targetEntity="OptionGroupOption", mappedBy="optionGroup", cascade={"persist"})
     * @ORM\OrderBy({"value" = "ASC", "id" = "ASC"})
     *
     * @var Collection<OptionGroupOption>
     */
    private Collection $options;

    /**
     * @ORM\OneToMany(targetEntity="Element", mappedBy="optionGroup")
     *
     * @var Collection<Element>
     */
    private Collection $elements;

    public function __construct(string $id, string $title, ?string $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->options = new ArrayCollection();
        $this->elements = new ArrayCollection();
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

    /** @return Collection<OptionGroupOption> */
    public function getOptions(): Collection
    {
        return $this->options;
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
}
