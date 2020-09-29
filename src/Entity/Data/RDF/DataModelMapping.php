<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_rdf_mappings")
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
     * @ORM\ManyToOne(targetEntity="RDFDistribution", inversedBy="mappings",cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     */
    private RDFDistribution $distribution;

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

    public function __construct(RDFDistribution $distribution, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        $this->distribution = $distribution;
        $this->entity = $entity;
        $this->dataModelVersion = $dataModelVersion;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function setDistribution(RDFDistribution $distribution): void
    {
        $this->distribution = $distribution;
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
}
