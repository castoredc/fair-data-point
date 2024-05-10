<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\NodeType;
use App\Entity\Version as VersionNumber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function assert;
use function is_a;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_version")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelVersion extends Version implements ModelVersion
{
    /**
     * @ORM\OneToMany(targetEntity="NamespacePrefix", mappedBy="metadataModel", cascade={"persist"})
     * @ORM\OrderBy({"prefix" = "ASC"})
     *
     * @var Collection<NamespacePrefix>
     */
    private Collection $prefixes;

    /**
     * @ORM\OneToMany(targetEntity="Predicate", mappedBy="metadataModel", cascade={"persist"})
     *
     * @var Collection<Predicate>
     */
    private Collection $predicates;

    /**
     * @ORM\OneToMany(targetEntity="MetadataModelForm", mappedBy="metadataModel", cascade={"persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     *
     * @var Collection<MetadataModelForm>
     */
    private Collection $forms;

    /**
     * @ORM\OneToMany(targetEntity="MetadataModelField", mappedBy="metadataModel", cascade={"persist"})
     *
     * @var Collection<MetadataModelField>
     */
    private Collection $fields;

    public function __construct(VersionNumber $version)
    {
        parent::__construct($version);

        $this->prefixes = new ArrayCollection();
        $this->predicates = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    /** @return Node[] */
    public function getNodesByType(NodeType $nodeType): array
    {
        $return = [];

        foreach ($this->elements as $node) {
            if (! is_a($node, $nodeType->getClassNameForMetadataModel())) {
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

        $predicate->setMetadataModel($this);
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

        $prefix->setMetadataModelVersion($this);
        $this->prefixes->add($prefix);
    }

    public function removePrefix(CommonNamespacePrefix $prefix): void
    {
        assert($prefix instanceof NamespacePrefix);

        $this->prefixes->removeElement($prefix);
    }

    public function getMetadataModel(): MetadataModel
    {
        assert($this->dataSpecification instanceof MetadataModel);

        return $this->dataSpecification;
    }

    public function addNode(CommonNode $node): void
    {
        assert($node instanceof Node);

        $this->addElement($node);
    }

    /** @return Collection<MetadataModelForm> */
    public function getForms(): Collection
    {
        return $this->forms;
    }

    /** @return Collection<MetadataModelField> */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addForm(MetadataModelForm $form): void
    {
        $newFormOrder = $form->getOrder();
        $newForms = new ArrayCollection();

        $order = 1;
        foreach ($this->forms as $currentForm) {
            /** @var MetadataModelForm $currentForm */
            $newOrder = $order >= $newFormOrder ? $order + 1 : $order;
            $currentForm->setOrder($newOrder);
            $newForms->add($currentForm);

            $order++;
        }

        $newForms->add($form);
        $this->forms = $newForms;
    }

    public function reorderForms(): void
    {
        $newForms = new ArrayCollection();
        $order = 1;

        foreach ($this->forms as $currentForm) {
            /** @var MetadataModelForm $currentForm */
            $currentForm->setOrder($order);
            $newForms->add($currentForm);

            $order++;
        }

        $this->forms = $newForms;
    }

    public function removeForm(MetadataModelForm $form): void
    {
        $this->forms->removeElement($form);

        $this->reorderForms();
    }
}
