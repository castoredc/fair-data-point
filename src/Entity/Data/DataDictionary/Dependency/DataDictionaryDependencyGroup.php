<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary\Dependency;

use App\Entity\Enum\DependencyCombinatorType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_key_exists;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_dependency_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryDependencyGroup extends DataDictionaryDependency
{
    /** @ORM\Column(type="DependencyCombinatorType") */
    private DependencyCombinatorType $combinator;

    /**
     * @ORM\OneToMany(targetEntity="DataDictionaryDependency", mappedBy="group", cascade={"persist", "remove"})
     *
     * @var Collection<DataDictionaryDependency>
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
     * @return Collection<DataDictionaryDependency>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(DataDictionaryDependency $rule): void
    {
        $this->rules->add($rule);
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): self
    {
        $group = new self(DependencyCombinatorType::fromString($data['combinator']));

        foreach ($data['rules'] as $rule) {
            if (array_key_exists('combinator', $rule)) {
                $newRule = self::fromData($rule);
            } else {
                $newRule = DataDictionaryDependencyRule::fromData($rule);
            }

            $newRule->setGroup($group);
            $group->addRule($newRule);
        }

        return $group;
    }
}
