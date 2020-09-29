<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DataModelModuleMapping extends DataModelMapping
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModelModule")
     * @ORM\JoinColumn(name="module", referencedColumnName="id")
     */
    private ?DataModelModule $module = null;

    public function __construct(RDFDistribution $distribution, DataModelModule $module, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        parent::__construct($distribution, $entity, $dataModelVersion);

        $this->module = $module;
    }

    public function getModule(): ?DataModelModule
    {
        return $this->module;
    }

    public function setModule(DataModelModule $module): void
    {
        $this->module = $module;
    }
}
