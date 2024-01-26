<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_prefix")
 * @ORM\HasLifecycleCallbacks
 */
class NamespacePrefix extends CommonNamespacePrefix
{
    /**
     * @ORM\ManyToOne(targetEntity="DataModelVersion", inversedBy="prefixes",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModel;

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModelVersion(DataModelVersion $dataModel): void
    {
        $this->dataModel = $dataModel;
    }
}
