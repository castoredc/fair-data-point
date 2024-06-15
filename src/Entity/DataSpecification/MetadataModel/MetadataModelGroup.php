<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Enum\ResourceType;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function assert;
use function iterator_to_array;
use function strcmp;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_module")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelGroup extends Group
{
    /** @ORM\Column(type="ResourceType") */
    private ResourceType $resourceType;

    public function __construct(string $title, int $order, ResourceType $resourceType, Version $version)
    {
        $this->resourceType = $resourceType;

        parent::__construct($title, $order, false, false, $version);
    }

    public function addTriple(Triple $triple): void
    {
        $this->addElementGroup($triple);
    }

    /** @return Collection<string, Triple> */
    public function getTriples(): Collection
    {
        $triples = $this->getElementGroups();

        $iterator = $triples->getIterator();
        assert($iterator instanceof ArrayIterator);

        $iterator->uasort(static function (Triple $a, Triple $b) {
            return strcmp(
                $a->getPredicate()->getIri()->getValue(),
                $b->getPredicate()->getIri()->getValue()
            );
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function setResourceType(ResourceType $resourceType): void
    {
        $this->resourceType = $resourceType;
    }
}
