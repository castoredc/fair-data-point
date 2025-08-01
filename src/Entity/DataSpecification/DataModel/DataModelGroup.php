<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Group;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_model_module')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
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
