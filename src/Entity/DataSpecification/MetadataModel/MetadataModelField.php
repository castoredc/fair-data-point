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
use function array_diff;
use function array_unique;
use function in_array;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_field")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelField
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /**
     * @ORM\ManyToOne(targetEntity="MetadataModelVersion", inversedBy="fields", cascade={"persist"})
     * @ORM\JoinColumn(name="metadata_model", referencedColumnName="id", nullable=false)
     */
    private MetadataModelVersion $metadataModel;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="string", nullable="true") */
    private ?string $description = null;

    /** @ORM\Column(name="orderNumber", type="integer", nullable=true) */
    private ?int $order;

    /**
     * @ORM\Column(type="ResourcesType", nullable="true")
     *
     * @var ResourceType[]
     */
    private array $resourceTypes;

    /**
     * @ORM\ManyToOne(targetEntity="MetadataModelForm", inversedBy="fields", cascade={"persist"})
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=false)
     */
    private MetadataModelForm $form;

    /** @ORM\Column(type="MetadataFieldType") */
    private MetadataFieldType $fieldType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup")
     * @ORM\JoinColumn(name="option_group_id", referencedColumnName="id")
     */
    private ?MetadataModelOptionGroup $optionGroup = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\Node\ValueNode")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
     */
    private ValueNode $node;

    /** @param ResourceType[] $resourceTypes */
    public function __construct(string $title, ?string $description, int $order, ValueNode $node, MetadataFieldType $fieldType, ?MetadataModelOptionGroup $optionGroup, array $resourceTypes, MetadataModelForm $form)
    {
        $this->title = $title;
        $this->description = $description;
        $this->order = $order;
        $this->node = $node;
        $this->fieldType = $fieldType;
        $this->optionGroup = $optionGroup;
        $this->resourceTypes = $resourceTypes;
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

    public function addResourceType(ResourceType $type): void
    {
        $this->resourceTypes[] = $type;
        $this->resourceTypes = array_unique($this->resourceTypes);
    }

    public function removeResourceType(ResourceType $type): void
    {
        $this->resourceTypes = array_diff($this->resourceTypes, [$type]);
    }

    /** @return ResourceType[] */
    public function getResourceTypes(): array
    {
        return $this->resourceTypes;
    }

    public function hasResourceType(ResourceType $type): bool
    {
        return in_array($type, $this->resourceTypes, true);
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
}
