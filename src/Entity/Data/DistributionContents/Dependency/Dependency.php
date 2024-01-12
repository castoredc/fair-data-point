<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents\Dependency;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_dependency")
 * @ORM\HasLifecycleCallbacks
 */
class Dependency
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
     * @ORM\ManyToOne(targetEntity="DependencyGroup", inversedBy="rules", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private ?DependencyGroup $group = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): ?DependencyGroup
    {
        return $this->group;
    }

    public function setGroup(?DependencyGroup $group): void
    {
        $this->group = $group;
    }
}
