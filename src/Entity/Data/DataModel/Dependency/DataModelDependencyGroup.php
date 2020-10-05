<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Dependency;

use App\Entity\Enum\DependencyCombinatorType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_key_exists;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_dependency_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelDependencyGroup extends DataModelDependency
{
    /** @ORM\Column(type="DependencyCombinatorType") */
    private DependencyCombinatorType $combinator;

    /**
     * @ORM\OneToMany(targetEntity="DataModelDependency", mappedBy="group", cascade={"persist", "remove"})
     *
     * @var Collection<DataModelDependency>
     */
    private Collection $rules;

    public function __construct(DependencyCombinatorType $combinator)
    {
        $this->combinator = $combinator;
        $this->rules = new ArrayCollection();
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

    public function addRule(DataModelDependency $rule): void
    {
        $this->rules->add($rule);
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): self
    {
        $group = new DataModelDependencyGroup(DependencyCombinatorType::fromString($data['combinator']));

        foreach ($data['rules'] as $rule) {
            if (array_key_exists('combinator', $rule)) {
                $newRule = self::fromData($rule);
            } else {
                $newRule = DataModelDependencyRule::fromData($rule);
            }

            $newRule->setGroup($group);
            $group->addRule($newRule);
        }

        return $group;
    }
}
