<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Dependency;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_specification_dependency_rule")
 * @ORM\HasLifecycleCallbacks
 */
class DependencyRule extends Dependency
{
    private string $elementId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\Common\Element", cascade={"persist"})
     * @ORM\JoinColumn(name="element", referencedColumnName="id", nullable=false)
     */
    private Element $element;

    /** @ORM\Column(type="DependencyOperatorType") */
    private DependencyOperatorType $operator;

    /** @ORM\Column(type="string") */
    private string $value;

    public function __construct(DependencyOperatorType $operator, string $value)
    {
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getElementId(): string
    {
        return $this->elementId;
    }

    public function setElementId(string $elementId): void
    {
        $this->elementId = $elementId;
    }

    public function getElement(): Element
    {
        return $this->element;
    }

    public function setElement(Element $element): void
    {
        $this->element = $element;
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

    /** @param array<mixed> $data */
    public static function fromData(array $data): self
    {
        $rule = new self(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setElementId($data['field']);

        return $rule;
    }
}
