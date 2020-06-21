<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataModel\Node\Node;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_triple")
 * @ORM\HasLifecycleCallbacks
 */
class Triple
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataModelModule", inversedBy="triples", cascade={"persist"})
     * @ORM\JoinColumn(name="module", referencedColumnName="id", nullable=false)
     *
     * @var DataModelModule
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node", cascade={"persist"})
     * @ORM\JoinColumn(name="subject", referencedColumnName="id", nullable=false)
     *
     * @var Node
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Predicate", cascade={"persist"})
     * @ORM\JoinColumn(name="predicate", referencedColumnName="id", nullable=false)
     *
     * @var Predicate
     */
    private $predicate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node", cascade={"persist"})
     * @ORM\JoinColumn(name="object", referencedColumnName="id", nullable=false)
     *
     * @var Node
     */
    private $object;

    public function __construct(DataModelModule $module, Node $subject, Predicate $predicate, Node $object)
    {
        $this->module = $module;
        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getModule(): DataModelModule
    {
        return $this->module;
    }

    public function setModule(DataModelModule $module): void
    {
        $this->module = $module;
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
}
