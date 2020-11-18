<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataSpecification\Group;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Study;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class GroupMapping extends Mapping
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataSpecification\Group")
     * @ORM\JoinColumn(name="group", referencedColumnName="id")
     */
    private ?Group $group = null;

    public function __construct(Study $study, Group $group, CastorEntity $entity, Version $version)
    {
        parent::__construct($study, $entity, $version);

        $this->group = $group;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }
}
