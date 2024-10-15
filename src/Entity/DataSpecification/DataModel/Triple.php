<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\Common\Model\Triple as CommonTriple;
use App\Entity\DataSpecification\DataModel\Node\Node;
use Doctrine\ORM\Mapping as ORM;
use function assert;

#[ORM\Table(name: 'data_model_triple')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Triple extends ElementGroup implements CommonTriple
{
    #[ORM\JoinColumn(name: 'subject', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Node::class, inversedBy: 'subjectTriples', cascade: ['persist'])]
    private Node $subject;

    #[ORM\JoinColumn(name: 'predicate', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Predicate::class, cascade: ['persist'])]
    private Predicate $predicate;

    #[ORM\JoinColumn(name: 'object', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Node::class, inversedBy: 'objectTriples', cascade: ['persist'])]
    private Node $object;

    public function __construct(DataModelGroup $module, Node $subject, Predicate $predicate, Node $object)
    {
        parent::__construct($module);

        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;
    }

    public function getSubject(): Node
    {
        return $this->subject;
    }

    public function setSubject(CommonNode $subject): void
    {
        assert($subject instanceof Node);

        $this->subject = $subject;
    }

    public function getPredicate(): Predicate
    {
        return $this->predicate;
    }

    public function setPredicate(CommonPredicate $predicate): void
    {
        assert($predicate instanceof Predicate);

        $this->predicate = $predicate;
    }

    public function getObject(): Node
    {
        return $this->object;
    }

    public function setObject(CommonNode $object): void
    {
        assert($object instanceof Node);

        $this->object = $object;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        $version = $this->getGroup()->getVersion();
        assert($version instanceof DataModelVersion);

        return $version;
    }

    public function getDataSpecificationVersion(): ModelVersion
    {
        return $this->getDataModelVersion();
    }
}
