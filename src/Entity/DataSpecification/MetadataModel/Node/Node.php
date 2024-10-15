<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Enum\NodeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;
use function array_unique;
use function assert;
use function count;
use const SORT_REGULAR;

#[ORM\Table(name: 'metadata_model_node')]
#[ORM\Entity(repositoryClass: \App\Repository\DataSpecification\MetadataModel\NodeRepository::class)]
#[ORM\HasLifecycleCallbacks]
abstract class Node extends Element implements CommonNode
{
    /**
     * @var Collection<Triple>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\DataSpecification\MetadataModel\Triple::class, mappedBy: 'subject')]
    private Collection $subjectTriples;

    /**
     * @var Collection<Triple>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\DataSpecification\MetadataModel\Triple::class, mappedBy: 'object')]
    private Collection $objectTriples;

    public function __construct(Version $version, string $title, ?string $description)
    {
        parent::__construct($version, $title, $description);

        $this->subjectTriples = new ArrayCollection();
        $this->objectTriples = new ArrayCollection();
    }

    public function getType(): ?NodeType
    {
        return null;
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        $version = $this->getVersion();
        assert($version instanceof MetadataModelVersion);

        return $version;
    }

    /** @return Triple[] */
    public function getTriples(): array
    {
        return array_unique(array_merge($this->subjectTriples->toArray(), $this->objectTriples->toArray()), SORT_REGULAR);
    }

    public function hasTriples(): bool
    {
        return count($this->getTriples()) > 0;
    }
}
