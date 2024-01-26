<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Group;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_module")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelGroup extends Group
{
    public function addTriple(Triple $triple): void
    {
        $this->addElementGroup($triple);
    }

    /** @return Collection<string, ElementGroup> */
    public function getTriples(): Collection
    {
        return $this->getElementGroups();
    }
}
