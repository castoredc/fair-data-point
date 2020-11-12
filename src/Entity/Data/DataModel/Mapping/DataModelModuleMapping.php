<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Study;
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

    public function __construct(Study $study, DataModelModule $module, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        parent::__construct($study, $entity, $dataModelVersion);

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
