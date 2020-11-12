<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary\Dependency;

use App\Entity\Data\DataDictionary\Variable;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_dependency_rule")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryDependencyRule extends DataDictionaryDependency
{
    private string $variableId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataDictionary\Variable", cascade={"persist"})
     * @ORM\JoinColumn(name="variable", referencedColumnName="id", nullable=false)
     */
    private Variable $variable;

    /** @ORM\Column(type="DependencyOperatorType") */
    private DependencyOperatorType $operator;

    /** @ORM\Column(type="string") */
    private string $value;

    public function __construct(DependencyOperatorType $operator, string $value)
    {
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getVariableId(): string
    {
        return $this->variableId;
    }

    public function setVariableId(string $variableId): void
    {
        $this->variableId = $variableId;
    }

    public function getVariable(): Variable
    {
        return $this->variable;
    }

    public function setVariable(Variable $variable): void
    {
        $this->variable = $variable;
    }

    public function getOperator(): DependencyOperatorType
    {
        return $this->operator;
    }

    public function setOperator(DependencyOperatorType $operator): void
    {
        $this->operator = $operator;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): self
    {
        $rule = new self(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setVariableId($data['variable']);

        return $rule;
    }
}
