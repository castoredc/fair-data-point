<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification_element")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model_externalIri" = "App\Entity\Data\DataModel\Node\ExternalIriNode",
 *     "model_internalIri" = "App\Entity\Data\DataModel\Node\InternalIriNode",
 *     "model_literal" = "App\Entity\Data\DataModel\Node\LiteralNode",
 *     "model_record" = "App\Entity\Data\DataModel\Node\RecordNode",
 *     "model_value" = "App\Entity\Data\DataModel\Node\ValueNode",
 *     "dictionary_variable" = "App\Entity\Data\DataDictionary\Variable",
 *     "node" = "App\Entity\Data\DataModel\Node\Node"
 * })
 */
abstract class Element
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", length=190)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id", nullable=true)
     */
    protected ?Group $group = null;

    /**
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="version", referencedColumnName="id", nullable=false)
     */
    private Version $version;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $description = null;

    /** @ORM\Column(name="orderNumber", type="integer", nullable=true) */
    protected ?int $order;

    /**
     * @ORM\ManyToOne(targetEntity="OptionGroup", inversedBy="elements")
     * @ORM\JoinColumn(name="option_group", referencedColumnName="id", nullable=true)
     */
    private ?OptionGroup $optionGroup;

    public function __construct(Version $version, string $title, ?string $description)
    {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
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

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
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
