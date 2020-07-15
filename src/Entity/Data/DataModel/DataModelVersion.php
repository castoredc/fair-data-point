<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\NodeType;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function is_a;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_version")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelVersion
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
     * @ORM\OneToMany(targetEntity="App\Entity\Data\RDF\RDFDistribution", mappedBy="currentDataModelVersion")
     *
     * @var Collection<RDFDistribution>
     */
    private $distributions;

    /**
     * @ORM\ManyToOne(targetEntity="DataModel", inversedBy="versions",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     *
     * @var DataModel
     */
    private $dataModel;

    /**
     * @ORM\Column(type="version")
     *
     * @var Version
     */
    private $version;

    public function __construct(Version $version)
    {
        $this->version = $version;

        $this->modules = new ArrayCollection();
        $this->prefixes = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->predicates = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
