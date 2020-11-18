<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification_element")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model" = "App\Entity\Data\DataModel\Node\Node",
 *     "dictionary" = "App\Entity\Data\DataDictionary\Variable",
 * })
 */
abstract class Element
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="group", referencedColumnName="id", nullable=true)
     */
    protected ?Group $group = null;

    /**
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="version", referencedColumnName="id", nullable=false)
     */
    private Version $version;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $description = null;

    /** @ORM\Column(name="`order`", type="integer") */
    protected int $order;

    public function __construct(Version $version, string $title, ?string $description)
    {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }
}
