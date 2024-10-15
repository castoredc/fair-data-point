<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\ResourceType;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_model_field')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataModelField
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MetadataModelVersion::class, inversedBy: 'fields', cascade: ['persist'])]
    private MetadataModelVersion $metadataModel;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    private ?int $order;

    #[ORM\Column(type: 'ResourceType')]
    private ResourceType $resourceType;

    #[ORM\JoinColumn(name: 'form_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MetadataModelForm::class, inversedBy: 'fields', cascade: ['persist'])]
    private MetadataModelForm $form;

    #[ORM\Column(type: 'MetadataFieldType')]
    private MetadataFieldType $fieldType;

    #[ORM\JoinColumn(name: 'option_group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MetadataModelOptionGroup::class)]
    private ?MetadataModelOptionGroup $optionGroup = null;

    #[ORM\JoinColumn(name: 'node_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\OneToOne(targetEntity: ValueNode::class, inversedBy: 'field')]
    private ValueNode $node;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isRequired = false;

    public function __construct(string $title, ?string $description, int $order, ValueNode $node, MetadataFieldType $fieldType, ?MetadataModelOptionGroup $optionGroup, ResourceType $resourceType, bool $isRequired, MetadataModelForm $form)
    {
        $this->title = $title;
        $this->description = $description;
        $this->order = $order;
        $this->node = $node;
        $this->fieldType = $fieldType;
        $this->optionGroup = $optionGroup;
        $this->resourceType = $resourceType;
        $this->isRequired = $isRequired;
        $this->form = $form;
        $this->metadataModel = $form->getMetadataModelVersion();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
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

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function setResourceType(ResourceType $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getNode(): ValueNode
    {
        return $this->node;
    }

    public function setNode(ValueNode $node): void
    {
        $this->node = $node;
    }

    public function getFieldType(): ?MetadataFieldType
    {
        return $this->fieldType;
    }

    public function setFieldType(?MetadataFieldType $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    public function getOptionGroup(): ?MetadataModelOptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(?MetadataModelOptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    public function getForm(): MetadataModelForm
    {
        return $this->form;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }
}
