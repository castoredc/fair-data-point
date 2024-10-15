<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents\Dependency;

use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\Enum\DependencyOperatorType;
use App\Entity\Enum\DistributionContentsDependencyType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'distribution_dependency_rule')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DependencyRule extends Dependency
{
    #[ORM\Column(type: 'DistributionContentsDependencyType')]
    private DistributionContentsDependencyType $type;

    private ?string $nodeId;

    #[ORM\JoinColumn(name: 'node', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\DataModel\Node\ValueNode::class, cascade: ['persist'])]
    private ?ValueNode $node;

    #[ORM\Column(type: 'DependencyOperatorType')]
    private DependencyOperatorType $operator;

    #[ORM\Column(type: 'string')]
    private string $value;

    public function __construct(DistributionContentsDependencyType $type, DependencyOperatorType $operator, string $value)
    {
        $this->type = $type;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getNodeId(): ?string
    {
        return $this->nodeId;
    }

    public function setNodeId(string $nodeId): void
    {
        $this->nodeId = $nodeId;
    }

    public function getNode(): ?ValueNode
    {
        return $this->node;
    }

    public function setNode(?ValueNode $node): void
    {
        $this->node = $node;
    }

    public function getType(): DistributionContentsDependencyType
    {
        return $this->type;
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
        $type = DistributionContentsDependencyType::fromString($data['field']['valueType']);

        $rule = new self(
            $type,
            DependencyOperatorType::fromString($data['operator']),
            $data['value']
        );

        if ($type->isValueNode()) {
            $rule->setNodeId($data['field']['value']);
        }

        return $rule;
    }
}
