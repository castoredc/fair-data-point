<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\MetadataDisplayType;
use App\Entity\Enum\ResourceType;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_model_display_setting')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataModelDisplaySetting
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MetadataModelVersion::class, inversedBy: 'displaySettings', cascade: ['persist'])]
    private MetadataModelVersion $metadataModel;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    private ?int $order;

    #[ORM\Column(type: 'ResourceType')]
    private ResourceType $resourceType;

    #[ORM\Column(type: 'MetadataDisplayType')]
    private MetadataDisplayType $displayType;

    #[ORM\Column(type: 'MetadataDisplayPosition')]
    private MetadataDisplayPosition $displayPosition;

    #[ORM\JoinColumn(name: 'node_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\OneToOne(targetEntity: ValueNode::class, inversedBy: 'displaySetting')]
    private ValueNode $node;

    public function __construct(
        string $title,
        int $order,
        ValueNode $node,
        MetadataDisplayType $displayType,
        MetadataDisplayPosition $position,
        ResourceType $resourceType,
        MetadataModelVersion $metadataModel,
    ) {
        $this->title = $title;
        $this->order = $order;
        $this->node = $node;
        $this->displayType = $displayType;
        $this->displayPosition = $position;
        $this->resourceType = $resourceType;
        $this->metadataModel = $metadataModel;
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

    public function getDisplayType(): MetadataDisplayType
    {
        return $this->displayType;
    }

    public function setDisplayType(MetadataDisplayType $displayType): void
    {
        $this->displayType = $displayType;
    }

    public function getDisplayPosition(): MetadataDisplayPosition
    {
        return $this->displayPosition;
    }

    public function setDisplayPosition(MetadataDisplayPosition $displayPosition): void
    {
        $this->displayPosition = $displayPosition;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModel;
    }
}
