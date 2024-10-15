<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use App\Entity\Metadata\Metadata;
use App\Entity\Version as VersionNumber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;
use function assert;
use function count;
use function is_a;

#[ORM\Table(name: 'metadata_model_version')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataModelVersion extends Version implements ModelVersion
{
    public const DCTERMS_TITLE = 'http://purl.org/dc/terms/title';
    public const DCTERMS_DESCRIPTION = 'http://purl.org/dc/terms/description';

    private const DEFAULT_ORDER = [
        MetadataDisplayPosition::TITLE => 1,
        MetadataDisplayPosition::DESCRIPTION => 1,
        MetadataDisplayPosition::SIDEBAR => 1,
        MetadataDisplayPosition::MODAL => 1,
    ];

    /** @var Collection<NamespacePrefix> */
    #[ORM\OneToMany(targetEntity: NamespacePrefix::class, mappedBy: 'metadataModel', cascade: ['persist'])]
    #[ORM\OrderBy(['prefix' => 'ASC'])]
    private Collection $prefixes;

    /** @var Collection<Predicate> */
    #[ORM\OneToMany(targetEntity: Predicate::class, mappedBy: 'metadataModel', cascade: ['persist'])]
    private Collection $predicates;

    /** @var Collection<MetadataModelForm> */
    #[ORM\OneToMany(targetEntity: MetadataModelForm::class, mappedBy: 'metadataModel', cascade: ['persist'])]
    #[ORM\OrderBy(['order' => 'ASC'])]
    private Collection $forms;

    /** @var Collection<MetadataModelField> */
    #[ORM\OneToMany(targetEntity: MetadataModelField::class, mappedBy: 'metadataModel', cascade: ['persist'])]
    private Collection $fields;

    /** @var Collection<MetadataModelDisplaySetting> */
    #[ORM\OneToMany(targetEntity: MetadataModelDisplaySetting::class, mappedBy: 'metadataModel', cascade: ['persist'])]
    #[ORM\OrderBy(['order' => 'ASC'])]
    private Collection $displaySettings;

    /** @var Collection<Metadata> */
    #[ORM\OneToMany(targetEntity: Metadata::class, mappedBy: 'metadataModelVersion', cascade: ['persist'])]
    private Collection $assignedMetadata;

    public function __construct(VersionNumber $version)
    {
        parent::__construct($version);

        $this->prefixes = new ArrayCollection();
        $this->predicates = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->assignedMetadata = new ArrayCollection();
        $this->displaySettings = new ArrayCollection();
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

    /** @return array<string, ValueNode> */
    public function getValueNodes(): array
    {
        $return = [];

        foreach ($this->elements as $node) {
            if (! $node instanceof ValueNode) {
                continue;
            }

            $return[$node->getTitle()] = $node;
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

    public function getValueNode(ResourceType $resourceType, string $predicate): ?ValueNode
    {
        $groups = $this->getGroupsForResourceType($resourceType);

        /** @var Triple[] $triples */
        $triples = [];

        foreach ($groups as $group) {
            $triples = array_merge($triples, $group->getTriples()->filter(static function (Triple $triple) use ($resourceType, $predicate) {
                $subject = $triple->getSubject();

                return $subject instanceof RecordNode
                    && $subject->getResourceType()->isEqualTo($resourceType)
                    && $triple->getPredicate()->getIri()->getValue() === $predicate;
            })->toArray());
        }

        if (count($triples) === 0) {
            return null;
        }

        $node = $triples[0]->getObject();
        assert($node instanceof ValueNode);

        return $node;
    }

    public function getTitleNode(ResourceType $resourceType): ?ValueNode
    {
        return $this->getValueNode($resourceType, self::DCTERMS_TITLE);
    }

    public function getDescriptionNode(ResourceType $resourceType): ?ValueNode
    {
        return $this->getValueNode($resourceType, self::DCTERMS_DESCRIPTION);
    }

    /** @return MetadataModelGroup[] */
    public function getGroupsForResourceType(ResourceType $resourceType): array
    {
        return $this->getGroups()->filter(static function (MetadataModelGroup $group) use ($resourceType) {
            return $group->getResourceType()->isEqualTo($resourceType);
        })->toArray();
    }

    public function addDisplaySetting(MetadataModelDisplaySetting $displaySetting): void
    {
        $this->reorderDisplaySetting($displaySetting);

        $this->displaySettings->add($displaySetting);
    }

    public function reorderDisplaySetting(?MetadataModelDisplaySetting $displaySetting = null): void
    {
        $newDisplaySettings = new ArrayCollection();

        $order = [
            ResourceType::FDP => self::DEFAULT_ORDER,
            ResourceType::CATALOG => self::DEFAULT_ORDER,
            ResourceType::DATASET => self::DEFAULT_ORDER,
            ResourceType::DISTRIBUTION => self::DEFAULT_ORDER,
            ResourceType::STUDY => self::DEFAULT_ORDER,
        ];

        foreach ($this->displaySettings as $currentDisplaySetting) {
            $currentOrder = $order[$currentDisplaySetting->getResourceType()->toString()][$currentDisplaySetting->getDisplayPosition()->toString()];

            if (
                $displaySetting !== null
                && $displaySetting->getResourceType()->isEqualTo($currentDisplaySetting->getResourceType())
                && $displaySetting->getDisplayPosition()->isEqualTo($currentDisplaySetting->getDisplayPosition())
            ) {
                $newOrder = $currentOrder >= $displaySetting->getOrder() ? $currentOrder + 1 : $currentOrder;
            } else {
                $newOrder = $currentOrder;
            }

            $currentDisplaySetting->setOrder($newOrder);
            $newDisplaySettings->add($currentDisplaySetting);

            $order[$currentDisplaySetting->getResourceType()->toString()][$currentDisplaySetting->getDisplayPosition()->toString()]++;
        }

        $this->displaySettings = $newDisplaySettings;
    }

    /** @return Collection<MetadataModelDisplaySetting> */
    public function getDisplaySettings(): Collection
    {
        return $this->displaySettings;
    }

    /** @return MetadataModelDisplaySetting[] */
    public function getDisplaySettingsForResourceType(ResourceType $resourceType): array
    {
        return $this->getDisplaySettings()->filter(static function (MetadataModelDisplaySetting $displaySetting) use ($resourceType) {
            return $displaySetting->getResourceType()->isEqualTo($resourceType);
        })->toArray();
    }

    public function removeDisplaySetting(MetadataModelDisplaySetting $displaySetting): void
    {
        $this->displaySettings->removeElement($displaySetting);

        $this->reorderDisplaySetting();
    }
}
