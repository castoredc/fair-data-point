<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node")
 * @ORM\HasLifecycleCallbacks
 */
abstract class Node
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModelVersion", inversedBy="nodes", cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModel;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $description = null;

    public function __construct(DataModelVersion $dataModel, string $title, ?string $description)
    {
        $this->dataModel = $dataModel;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModelVersion $dataModel): void
    {
        $this->dataModel = $dataModel;
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

    public function getType(): ?NodeType
    {
        return null;
    }

    public function getValue(): ?string
    {
        return null;
    }
}
