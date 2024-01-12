<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification\Mapping;

use App\Entity\Data\DataSpecification\Version;
use App\Entity\Study;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_specification_mappings")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"element" = "ElementMapping", "group" = "GroupMapping"})
 */
abstract class Mapping
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Study", inversedBy="mappings",cascade={"persist"})
     * @ORM\JoinColumn(name="study", referencedColumnName="id", nullable=false)
     */
    private Study $study;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataSpecification\Version")
     * @ORM\JoinColumn(name="version", referencedColumnName="id", nullable=false)
     */
    private Version $version;

    public function __construct(Study $study, Version $version)
    {
        $this->study = $study;
        $this->version = $version;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): void
    {
        $this->study = $study;
    }
}
