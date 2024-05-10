<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Enum\ResourceType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_diff;
use function array_unique;
use function in_array;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_module")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelGroup extends Group
{
    /**
     * @ORM\Column(type="ResourcesType", nullable="true")
     *
     * @var ResourceType[]
     */
    private ?array $resourceTypes = null;

    public function __construct(string $title, int $order, Version $version)
    {
        parent::__construct($title, $order, false, false, $version);
    }

    public function addTriple(Triple $triple): void
    {
        $this->addElementGroup($triple);
    }

    /** @return Collection<string, ElementGroup> */
    public function getTriples(): Collection
    {
        return $this->getElementGroups();
    }

    public function addResourceType(ResourceType $type): void
    {
        if ($this->resourceTypes === null) {
            $this->resourceTypes = [];
        }

        $this->resourceTypes[] = $type;
        $this->resourceTypes = array_unique($this->resourceTypes);
    }

    public function removeResourceType(ResourceType $type): void
    {
        if ($this->resourceTypes === null) {
            $this->resourceTypes = [];
        }

        $this->resourceTypes = array_diff($this->resourceTypes, [$type]);
    }

    /** @return ResourceType[] */
    public function getResourceTypes(): array
    {
        return $this->resourceTypes;
    }

    public function hasResourceType(ResourceType $type): bool
    {
        if ($this->resourceTypes === null) {
            return false;
        }

        return in_array($type, $this->resourceTypes);
    }
}
