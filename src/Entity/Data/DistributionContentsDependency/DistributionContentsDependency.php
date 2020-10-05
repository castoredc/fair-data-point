<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContentsDependency;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_dependency")
 * @ORM\HasLifecycleCallbacks
 */
class DistributionContentsDependency
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DistributionContentsDependencyGroup", inversedBy="rules", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private ?DistributionContentsDependencyGroup $group = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): ?DistributionContentsDependencyGroup
    {
        return $this->group;
    }

    public function setGroup(?DistributionContentsDependencyGroup $group): void
    {
        $this->group = $group;
    }
}
