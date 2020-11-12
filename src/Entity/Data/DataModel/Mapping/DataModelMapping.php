<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Study;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_mappings")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"node" = "DataModelNodeMapping", "module" = "DataModelModuleMapping"})
 */
abstract class DataModelMapping
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModelVersion")
     * @ORM\JoinColumn(name="data_model_version", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModelVersion;

    public function __construct(Study $study, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        $this->study = $study;
        $this->entity = $entity;
        $this->dataModelVersion = $dataModelVersion;
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

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
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
