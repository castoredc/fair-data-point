<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\ElementGroup;
use App\Entity\DataSpecification\Common\Model\Node as CommonNode;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\Common\Model\Triple as CommonTriple;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_triple")
 * @ORM\HasLifecycleCallbacks
 */
class Triple extends ElementGroup implements CommonTriple
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\Node\Node", inversedBy="subjectTriples", cascade={"persist"})
     * @ORM\JoinColumn(name="subject", referencedColumnName="id", nullable=false)
     */
    private Node $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Predicate", cascade={"persist"})
     * @ORM\JoinColumn(name="predicate", referencedColumnName="id", nullable=false)
     */
    private Predicate $predicate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\Node\Node", inversedBy="objectTriples", cascade={"persist"})
     * @ORM\JoinColumn(name="object", referencedColumnName="id", nullable=false)
     */
    private Node $object;

    public function __construct(MetadataModelGroup $module, Node $subject, Predicate $predicate, Node $object)
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

    public function getDataModelVersion(): MetadataModelVersion
    {
        $version = $this->getGroup()->getVersion();
        assert($version instanceof MetadataModelVersion);

        return $version;
    }
}
