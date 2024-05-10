<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Enum\ResourceType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    /** @return Collection<string, ElementGroup> */
    public function getTriples(): Collection
    {
        return $this->getElementGroups();
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
