<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataSpecification\ElementGroup;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_triple")
 * @ORM\HasLifecycleCallbacks
 */
class Triple extends ElementGroup
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node", cascade={"persist"})
     * @ORM\JoinColumn(name="subject", referencedColumnName="id", nullable=false)
     */
    private Node $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Predicate", cascade={"persist"})
     * @ORM\JoinColumn(name="predicate", referencedColumnName="id", nullable=false)
     */
    private Predicate $predicate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node", cascade={"persist"})
     * @ORM\JoinColumn(name="object", referencedColumnName="id", nullable=false)
     */
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

    public function setSubject(Node $subject): void
    {
        $this->subject = $subject;
    }

    public function getPredicate(): Predicate
    {
        return $this->predicate;
    }

    public function setPredicate(Predicate $predicate): void
    {
        $this->predicate = $predicate;
    }

    public function getObject(): Node
    {
        return $this->object;
    }

    public function setObject(Node $object): void
    {
        $this->object = $object;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        $version = $this->getGroup()->getVersion();
        assert($version instanceof DataModelVersion);

        return $version;
    }
}
