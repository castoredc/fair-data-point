<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\NodeType;
use App\Entity\Version as VersionNumber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function assert;
use function is_a;

#[ORM\Table(name: 'data_model_version')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DataModelVersion extends Version implements ModelVersion
{
    /** @var Collection<NamespacePrefix> */
    #[ORM\OneToMany(targetEntity: NamespacePrefix::class, mappedBy: 'dataModel', cascade: ['persist'])]
    #[ORM\OrderBy(['prefix' => 'ASC'])]
    private Collection $prefixes;

    /** @var Collection<Predicate> */
    #[ORM\OneToMany(targetEntity: Predicate::class, mappedBy: 'dataModel', cascade: ['persist'])]
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
            if (! is_a($node, $nodeType->getClassNameForDataModel())) {
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

    public function addPredicate(CommonPredicate $predicate): void
    {
        assert($predicate instanceof Predicate);

        $predicate->setDataModel($this);
        $this->predicates->add($predicate);
    }

    /** @return Collection<NamespacePrefix> */
    public function getPrefixes(): Collection
    {
        return $this->prefixes;
    }

    public function addPrefix(CommonNamespacePrefix $prefix): void
    {
        assert($prefix instanceof NamespacePrefix);

        $prefix->setDataModelVersion($this);
        $this->prefixes->add($prefix);
    }

    public function removePrefix(CommonNamespacePrefix $prefix): void
    {
        assert($prefix instanceof NamespacePrefix);

        $this->prefixes->removeElement($prefix);
    }

    public function getDataModel(): DataModel
    {
        assert($this->dataSpecification instanceof DataModel);

        return $this->dataSpecification;
    }

    public function addNode(CommonNode $node): void
    {
        assert($node instanceof Node);

        $this->addElement($node);
    }
}
