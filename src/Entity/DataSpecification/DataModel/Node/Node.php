<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel\Node;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\NodeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;
use function array_unique;
use function assert;
use function count;
use const SORT_REGULAR;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 * @ORM\Table(name="data_model_node")
 * @ORM\HasLifecycleCallbacks
 */
abstract class Node extends Element
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DataModel\Triple", mappedBy="subject")
     *
     * @var Collection<Triple>
     */
    private Collection $subjectTriples;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DataModel\Triple", mappedBy="object")
     *
     * @var Collection<Triple>
     */
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

    public function getDataModelVersion(): DataModelVersion
    {
        $version = $this->getVersion();
        assert($version instanceof DataModelVersion);

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
