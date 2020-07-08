<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\NodeType;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function is_a;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model")
 * @ORM\HasLifecycleCallbacks
 */
class DataModel
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="DataModelModule", mappedBy="dataModel", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"order" = "ASC", "id" = "ASC"})
     *
     * @var Collection<DataModelModule>
     */
    private $modules;

    /**
     * @ORM\OneToMany(targetEntity="NamespacePrefix", mappedBy="dataModel", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<NamespacePrefix>
     */
    private $prefixes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DataModel\Node\Node", mappedBy="dataModel", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<Node>
     */
    private $nodes;

    /**
     * @ORM\OneToMany(targetEntity="Predicate", mappedBy="dataModel", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<Predicate>
     */
    private $predicates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\RDF\RDFDistribution", mappedBy="dataModel")
     *
     * @var Collection<RDFDistribution>
     */
    private $distributions;

    public function __construct(string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;

        $this->modules = new ArrayCollection();
        $this->prefixes = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->predicates = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    /**
     * @return Collection<Node>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    /**
     * @return Node[]
     */
    public function getNodesByType(NodeType $nodeType): array
    {
        $return = [];

        foreach ($this->nodes as $node) {
            if (! is_a($node, $nodeType->getClassName())) {
                continue;
            }

            $return[] = $node;
        }

        return $return;
    }

    /**
     * @return Collection<Predicate>
     */
    public function getPredicates(): Collection
    {
        return $this->predicates;
    }

    /**
     * @return Collection<DataModelModule>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(DataModelModule $module): void
    {
        $newModuleOrder = $module->getOrder();
        $newModules = new ArrayCollection();

        $order = 1;
        foreach ($this->modules as $currentModule) {
            /** @var DataModelModule $currentModule */
            $newOrder = $order >= $newModuleOrder ? ($order + 1) : $order;
            $currentModule->setOrder($newOrder);
            $newModules->add($currentModule);

            $order++;
        }

        $newModules->add($module);
        $this->modules = $newModules;
    }

    public function reorderModules(): void
    {
        $newModules = new ArrayCollection();
        $order = 1;

        foreach ($this->modules as $currentModule) {
            /** @var DataModelModule $currentModule */
            $currentModule->setOrder($order);
            $newModules->add($currentModule);

            $order++;
        }

        $this->modules = $newModules;
    }

    public function removeModule(DataModelModule $module): void
    {
        $this->modules->removeElement($module);

        $this->reorderModules();
    }

    /**
     * @return Collection<NamespacePrefix>
     */
    public function getPrefixes(): Collection
    {
        return $this->prefixes;
    }

    public function addPrefix(NamespacePrefix $prefix): void
    {
        $prefix->setDataModel($this);
        $this->prefixes->add($prefix);
    }

    public function removePrefix(NamespacePrefix $prefix): void
    {
        $this->prefixes->removeElement($prefix);
    }

    public function addNode(Node $node): void
    {
        $node->setDataModel($this);
        $this->nodes->add($node);
    }

    public function removeNode(Node $node): void
    {
        $this->nodes->removeElement($node);
    }

    /**
     * @return Collection<RDFDistribution>
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }
}
