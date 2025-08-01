<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Study;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_specification_mappings_group')]
#[ORM\Entity]
class GroupMapping extends Mapping
{
    #[ORM\JoinColumn(name: 'groupId', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    private ?Group $group = null;

    #[ORM\JoinColumn(name: 'entity', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: CastorEntity::class)]
    private CastorEntity $entity;

    public function __construct(Study $study, Group $group, CastorEntity $entity, Version $version)
    {
        parent::__construct($study, $version);

        $this->entity = $entity;
        $this->group = $group;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function setEntity(CastorEntity $entity): void
    {
        $this->entity = $entity;
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
