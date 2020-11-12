<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary\Dependency;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_dictionary_dependency")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryDependency
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionaryDependencyGroup", inversedBy="rules", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private ?DataDictionaryDependencyGroup $group = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): ?DataDictionaryDependencyGroup
    {
        return $this->group;
    }

    public function setGroup(?DataDictionaryDependencyGroup $group): void
    {
        $this->group = $group;
    }
}
