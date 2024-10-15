<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_model_prefix')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class NamespacePrefix extends CommonNamespacePrefix
{
    #[ORM\JoinColumn(name: 'data_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: DataModelVersion::class, inversedBy: 'prefixes', cascade: ['persist'])]
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
