<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Dependency;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_dependency")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelDependency
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataModelDependencyGroup", inversedBy="rules", cascade={"persist"})
     * @ORM\JoinColumn(name="group", referencedColumnName="id")
     *
     * @var DataModelDependencyGroup|null
     */
    private $group;

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): ?DataModelDependencyGroup
    {
        return $this->group;
    }

    public function setGroup(?DataModelDependencyGroup $group): void
    {
        $this->group = $group;
    }
}
