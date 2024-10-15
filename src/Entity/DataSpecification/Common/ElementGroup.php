<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification_elementgroup')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['model' => 'App\Entity\DataSpecification\DataModel\Triple', 'metadata_model' => 'App\Entity\DataSpecification\MetadataModel\Triple'])]
abstract class ElementGroup
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'groupId', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'elementGroups', cascade: ['persist'])]
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }
}
