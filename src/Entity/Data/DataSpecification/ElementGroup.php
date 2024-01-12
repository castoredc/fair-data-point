<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification_elementgroup")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model" = "App\Entity\Data\DataModel\Triple",
 * })
 */
abstract class ElementGroup
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", length=190)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="elementGroups", cascade={"persist"})
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id", nullable=false)
     */
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getId(): string
    {
        return $this->id;
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
