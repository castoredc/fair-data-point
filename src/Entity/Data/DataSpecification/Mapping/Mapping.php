<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Study;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_specification_mappings")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"element" = "ElementMapping", "group" = "GroupMapping"})
 */
abstract class Mapping
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Study", inversedBy="mappings",cascade={"persist"})
     * @ORM\JoinColumn(name="study", referencedColumnName="id", nullable=false)
     */
    private Study $study;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\CastorEntity")
     * @ORM\JoinColumn(name="entity", referencedColumnName="id", nullable=false)
     */
    private CastorEntity $entity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataSpecification\Version")
     * @ORM\JoinColumn(name="version", referencedColumnName="id", nullable=false)
     */
    private Version $version;

    public function __construct(Study $study, CastorEntity $entity, Version $version)
    {
        $this->study = $study;
        $this->entity = $entity;
        $this->version = $version;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function setEntity(CastorEntity $entity): void
    {
        $this->entity = $entity;
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
