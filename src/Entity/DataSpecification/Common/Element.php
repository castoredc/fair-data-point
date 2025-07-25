<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification_element')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['node' => 'App\Entity\DataSpecification\DataModel\Node\Node', 'model_externalIri' => 'App\Entity\DataSpecification\DataModel\Node\ExternalIriNode', 'model_internalIri' => 'App\Entity\DataSpecification\DataModel\Node\InternalIriNode', 'model_literal' => 'App\Entity\DataSpecification\DataModel\Node\LiteralNode', 'model_record' => 'App\Entity\DataSpecification\DataModel\Node\RecordNode', 'model_value' => 'App\Entity\DataSpecification\DataModel\Node\ValueNode', 'dictionary_variable' => 'App\Entity\DataSpecification\DataDictionary\Variable', 'metadata_model_node' => 'App\Entity\DataSpecification\MetadataModel\Node\Node', 'metadata_model_externalIri' => 'App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode', 'metadata_model_internalIri' => 'App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode', 'metadata_model_literal' => 'App\Entity\DataSpecification\MetadataModel\Node\LiteralNode', 'metadata_model_record' => 'App\Entity\DataSpecification\MetadataModel\Node\RecordNode', 'metadata_model_value' => 'App\Entity\DataSpecification\MetadataModel\Node\ValueNode', 'metadata_model_children' => 'App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode', 'metadata_model_parents' => 'App\Entity\DataSpecification\MetadataModel\Node\ParentsNode'])]
abstract class Element
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'groupId', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'elements', cascade: ['persist'])]
    protected ?Group $group = null;

    #[ORM\JoinColumn(name: 'version', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Version::class, inversedBy: 'elements', cascade: ['persist'])]
    private Version $version;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    protected ?int $order;

    #[ORM\JoinColumn(name: 'option_group', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: OptionGroup::class, inversedBy: 'elements')]
    private ?OptionGroup $optionGroup = null;

    public function __construct(Version $version, string $title, ?string $description)
    {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId(): string
    {
        return (string) $this->id;
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
