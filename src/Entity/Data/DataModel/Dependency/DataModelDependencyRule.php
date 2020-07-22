<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Dependency;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\DependencyOperatorType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_dependency_rule")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelDependencyRule extends DataModelDependency
{
    /** @var string */
    private $nodeId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\ValueNode", cascade={"persist"})
     * @ORM\JoinColumn(name="node", referencedColumnName="id", nullable=false)
     *
     * @var ValueNode
     */
    private $node;

    /**
     * @ORM\Column(type="DependencyOperatorType")
     *
     * @var DependencyOperatorType
     */
    private $operator;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $value;

    public function __construct(DependencyOperatorType $operator, string $value)
    {
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public function setNodeId(string $nodeId): void
    {
        $this->nodeId = $nodeId;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function setNode(ValueNode $node): void
    {
        $this->node = $node;
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
        $rule = new DataModelDependencyRule(
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        $rule->setNodeId($data['field']);

        return $rule;
    }
}
