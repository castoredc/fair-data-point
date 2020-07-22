<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Dependency;

use App\Entity\Enum\DependencyCombinatorType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_dependency_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelDependencyGroup extends DataModelDependency
{
    /**
     * @ORM\Column(type="DependencyCombinatorType")
     *
     * @var DependencyCombinatorType
     */
    private $combinator;

    /**
     * @ORM\OneToMany(targetEntity="DataModelDependency", mappedBy="group", cascade={"persist"})
     *
     * @var Collection<DataModelDependency>
     */
    private $rules;

    /**
     * @param Collection<DataModelDependency> $rules
     */
    public function __construct(DependencyCombinatorType $combinator, Collection $rules)
    {
        $this->combinator = $combinator;
        $this->rules = $rules;
    }

    public function getCombinator(): DependencyCombinatorType
    {
        return $this->combinator;
    }

    public function setCombinator(DependencyCombinatorType $combinator): void
    {
        $this->combinator = $combinator;
    }

    /**
     * @return Collection<DataModelDependency>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * @param Collection<DataModelDependency> $rules
     */
    public function setRules(Collection $rules): void
    {
        $this->rules = $rules;
    }
}
