<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\Enum\ResourceType;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_model_form')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataModelForm
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MetadataModelVersion::class, inversedBy: 'forms', cascade: ['persist'])]
    private MetadataModelVersion $metadataModel;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(name: 'orderNumber', type: 'integer', nullable: true)]
    private ?int $order;

    #[ORM\Column(type: 'ResourceType')]
    private ResourceType $resourceType;

    /** @var Collection<MetadataModelField> */
    #[ORM\OneToMany(targetEntity: MetadataModelField::class, mappedBy: 'form', cascade: ['persist'])]
    #[ORM\OrderBy(['order' => 'ASC'])]
    private Collection $fields;

    public function __construct(string $title, int $order, ResourceType $resourceType, MetadataModelVersion $version)
    {
        $this->title = $title;
        $this->order = $order;
        $this->resourceType = $resourceType;
        $this->metadataModel = $version;

        $this->fields = new ArrayCollection();
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

    /** @return Collection<string, MetadataModelField> */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function setResourceType(ResourceType $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModel;
    }

    public function setMetadataModelVersion(MetadataModelVersion $metadataModelVersion): void
    {
        $this->metadataModel = $metadataModelVersion;
    }

    public function addField(MetadataModelField $field): void
    {
        $newFieldOrder = $field->getOrder();
        $newFields = new ArrayCollection();

        $order = 1;
        foreach ($this->fields as $currentField) {
            /** @var MetadataModelField $currentField */
            $newOrder = $order >= $newFieldOrder ? $order + 1 : $order;
            $currentField->setOrder($newOrder);
            $newFields->add($currentField);

            $order++;
        }

        $newFields->add($field);
        $this->fields = $newFields;
    }

    public function reorderFields(): void
    {
        $newFields = new ArrayCollection();
        $order = 1;

        foreach ($this->fields as $currentField) {
            /** @var MetadataModelField $currentField */
            $currentField->setOrder($order);
            $newFields->add($currentField);

            $order++;
        }

        $this->fields = $newFields;
    }

    public function removeField(MetadataModelField $field): void
    {
        $this->fields->removeElement($field);

        $this->reorderFields();
    }
}
