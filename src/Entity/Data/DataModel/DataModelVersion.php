<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Enum\NodeType;
use App\Entity\Version as VersionNumber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function assert;
use function is_a;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_version")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelVersion extends Version
{
    /**
     * @ORM\OneToMany(targetEntity="NamespacePrefix", mappedBy="dataModel", cascade={"persist"})
     *
     * @var Collection<NamespacePrefix>
     */
    private Collection $prefixes;

    /**
     * @ORM\OneToMany(targetEntity="Predicate", mappedBy="dataModel", cascade={"persist"})
     *
     * @var Collection<Predicate>
     */
    private Collection $predicates;

    public function __construct(VersionNumber $version)
    {
        parent::__construct($version);

        $this->prefixes = new ArrayCollection();
        $this->predicates = new ArrayCollection();
    }

    /** @return Node[] */
    public function getNodesByType(NodeType $nodeType): array
    {
        $return = [];

        foreach ($this->elements as $node) {
            if (! is_a($node, $nodeType->getClassName())) {
                continue;
            }

            assert($node instanceof Node);

            $return[] = $node;
        }

        return $return;
    }

    /** @return Collection<Predicate> */
    public function getPredicates(): Collection
    {
        return $this->predicates;
    }

    public function addPredicate(Predicate $predicate): void
    {
        $predicate->setDataModel($this);
        $this->predicates->add($predicate);
    }

    /** @return Collection<NamespacePrefix> */
    public function getPrefixes(): Collection
    {
        return $this->prefixes;
    }

    public function addPrefix(NamespacePrefix $prefix): void
    {
        $prefix->setDataModelVersion($this);
        $this->prefixes->add($prefix);
    }

    public function removePrefix(NamespacePrefix $prefix): void
    {
        $this->prefixes->removeElement($prefix);
    }

    public function getDataModel(): DataModel
    {
        assert($this->dataSpecification instanceof DataModel);

        return $this->dataSpecification;
    }

    public function addNode(Node $node): void
    {
        $this->addElement($node);
    }
}
