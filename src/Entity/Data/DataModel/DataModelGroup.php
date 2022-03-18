<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataSpecification\ElementGroup;
use App\Entity\Data\DataSpecification\Group;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_module")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelGroup extends Group
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
